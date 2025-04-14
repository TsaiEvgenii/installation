define([
    'jquery',
    '../render/canvas2d',
    './config',
    './context',
    './customization',
    '../data/helper',
    '../ui/helper/html',
    './widgets/toolbar',
    '../object/id'
], function(
    $,
    Drawer,
    Config,
    Context,
    Customization,
    DataHelper,
    HtmlHelper,
    Toolbar,
    ObjectId) {

    class Customizer {
        constructor(rootElement, config = {}) {
            this._rootElement = rootElement;
            this._rootWidgets = [];
            this.initContext(rootElement, config);
            this.initElements();
            this.initDrawer();
            this.initWidgets();

            // {paramId: [measurement1, measurement2, ...]}
            this._measurementsByParamId  = {};
            this._restrictionsByOptionId = {};


            // this._measurementRestrictionsByOptionId = {};

            this._parameterDefaults = new ObjectId.Map();
            // {optionId: [parameter1, parameter2]}
            this._parametersByOptionIds = {};

            // {groupId: selectedOptionId}
            this._selectedOptions = {};

            this._optionSelectHandlers = {};
            this._updateMeasurementHandlers = [];
            this._extOptionToggleHandlers = [];


            // this._extMeasurementsMinMaxHandlers = [];

            window.customizer = this; // TEST
        }


        // Initialization

        initContext(rootElement, config) {
            let ctx = new Context(rootElement, DataHelper.merged(Config, config));
            ctx.eventManager.subscribe(this, 'object');
            ctx.eventManager.subscribe(this, 'context');
            this._context = ctx;
        }

        initElements() {
            let ef = this._context.elementFactory;
            // Top
            let top = ef.make('div');
            HtmlHelper.addClassName(top, 'top');
            this._rootElement.appendChild(top);
            this._topElement = top;

            // Content
            let main = ef.make('div');
            HtmlHelper.addClassName(main, 'main');
            this._rootElement.appendChild(main);
            this._mainElement = main;

            // Canvas
            let canvas = ef.make('canvas', this._context.config.canvas);
            this._mainElement.appendChild(canvas);
            this._canvas = canvas;
        }

        initDrawer() {
            let drawer = new Drawer.Drawer(this._canvas),
                customization = new Customization.Customizer(this._context),
                scale = this._context.config.scale,
                font = this._context.config.font;
            drawer.scale = scale;
            drawer.scaleMultiplier = this._context.config.scaleMultiplier;
            drawer.fontFamily = font ? (font.family || null) : null;
            drawer.fontSize = font ? (font.size || null) : null;
            drawer.mobileFontSize =  font ? (font.mobileSize || null) : null;
            drawer.mobileBreakpoint = this._context.config.mobileBreakpoint || null;
            this._context.scale = scale;
            drawer.addAfterDraw(customization.customize.bind(customization));
            drawer.backgroundColor = this._context.config.background;
            drawer.setSmoothing(this._context.config.smoothing);
            drawer.reset();
            this._drawer = drawer;
        }

        initWidgets() {
            let ctx = this._context;

            // Toolbar
            let toolbar = new Toolbar.Widget(ctx);
            toolbar.addTools(this._context.config.toolbar);
            this._topElement.appendChild(toolbar.element);
            this._rootWidgets.push(toolbar);
        }


        // Event handling

        onEvent(event) {
            switch (event.type) {
                case 'context':
                    this._onContextEvent(event);
                    break;
                case 'object':
                    this._onObjectEvent(event);
                    break;
            }
            this.redraw();
        }

        _onObjectEvent(event) {
            if (event.name == 'changed') {
                let oh = this._context.objectHelper,
                    om = this._context.objectManager,
                    object = om.get(event.data.id);

                // Update linked objects
                this._context.linkManager.updateRefObjects(object.objectId);

                this._adjustScale();

                // Redraw
                this.redraw();

                // Update block measurements and restrictions and measurement restrictions
                if (oh.getType(object) == 'block') {
                    this._updateBlockMeasurements(object);
                    this._updateBlockRestrictions(object);
                    // this._updateBlockMeasurementRestrictions(object);
                }
            }
        }

        _onContextEvent(event) {
            if (event.name == 'changed') {
                if (event.data.scale != undefined) {
                    this._drawer.scale = event.data.scale;
                }
            }
        }


        // Import/export

        importData(data) {
            // Create objects
            let objects = this._context.dataImport.createAll(data);

            // Add root objects
            objects.forEach(function(object) {
                this._context.rootObjectIds.add(object.objectId);
                // initialize objects
                this._context.objectHelper.forEach(object, this._initObject.bind(this));
            }, this);

            // Update all linked objects
            this._context.linkManager.updateAllObjects();

            // Adjust drawer scale
            this._adjustScale();

            // Redraw
            this.redraw();
        }

        exportImage() {
            // Set tmp canvas
            let ef = this._context.elementFactory,
                canvasTmp = ef.make('canvas', this._context.config.canvas),
                canvas = this._canvas;
            this._canvas = canvasTmp;
            this._drawer.canvas = canvasTmp;

            // Draw
            this._drawer.editMode = true;
            this._adjustScale(true);
            let currentFontSize = this._drawer.fontSize;
            this._drawer.fontSize = currentFontSize * 2;
            this.redraw();
            let result = this._drawer.exportImage();

            // Restore canvas
            this._canvas = canvas;
            this._drawer.canvas = canvas;

            // Redraw
            this._drawer.editMode = false;
            this._drawer.fontSize = currentFontSize;
            this._adjustScale();
            this.redraw();

            return result;
        }

        exportNamedMeasurements() {
            let result = {},
                namedMeasurements = this._context.measurementManager.getAllByName();
            for (let name in namedMeasurements) {
                let measurement = namedMeasurements[name];
                result[name] = measurement.getValue();
            }
            return result;
        }

        importNamedMeasurements(params) {
            let measurementManager = this._context.measurementManager;
            for (let name in params) {
                let value = params[name],
                    measurement = measurementManager.getByName(name);
                if (measurement) {
                    measurement.setInputValue(value);
                }
            }
            this.redraw();
        }

        validateMeasurements() {
            return this._context.measurementManager.validate();
        }

        _initObject(object) {
            switch (object.objectId.type) {
            case 'measurement':
                this._initMeasurement(object);
                break;
            case 'parameter':
                this._initParameter(object);
                break;
            case 'measurement_restriction':
                this._initMeasurementRestriction(object);
                break;
            case 'link':
                this._initLink(object);
            }
        }


        // Parameters

        _initParameter(parameter) {
            let object = parameter.parent,
                options = (parameter.options || []);

            // Add option select handlers
            options.forEach(function(option) {
                this._addOptionSelectHandler(option.id, function() {
                    this._onOptionSelect(parameter, option.id, option.value, option.key_family);
                }.bind(this));
            }, this);

            // Store defaults
            {
                let defaultValue = DataHelper.getField(object, parameter.getName());
                this._parameterDefaults.set(parameter.objectId, defaultValue);
            }

            // Parameters by option ID
            {
                let map = this._parametersByOptionIds;
                options.forEach(function(option) {
                    map[option.id] || (map[option.id] = []);
                    map[option.id].push(parameter);
                });
            }
        }

        _onOptionSelect(parameter, optionId, optionValue, keyFamily) {
            if(keyFamily) {
                optionValue = this._getSingleOptionData(parameter, optionId) || optionValue;
            }
            let otherValue = this._checkParameterOption(parameter, optionId);
            optionValue = otherValue ? otherValue : optionValue;
            // Update parent object
            let object = parameter.parent;
            DataHelper.setField(object, parameter.getName(), optionValue);
            // Send event
            let data = {};
            data[parameter.getName()] = optionValue;
            this._context.eventManager.notify('object', 'changed', {
                id: object.objectId,
                parentId: this._context.objectHelper.getParentId(object),
                data: data
            });
        }

        _getSingleOptionData(parameter, optionId) {
            let option = parameter._options.find((opt) => {return opt.option_type_id === optionId && !opt.key_family});
            return option ? option.value : null;
        }

        _checkParameterOption(parameter, optionId) {
            let chosenOptionObjs = parameter._options.filter(function(option){
                return option.option_type_id === optionId && option.key_family;
            });
            let optionFamilies = {},
                value = null;
            chosenOptionObjs.forEach(function(optionWithKey) {
                let key = optionWithKey.key_family;
                optionFamilies[key]=[];
                parameter._options.forEach((option) => {
                    if(option.key_family && option.key_family === key) {
                        optionFamilies[key].push(option.option_type_id);
                    }
                });
            });
            Object.keys(optionFamilies).some(key => {
                let amountFamilyOptions = optionFamilies[key].length;
                optionFamilies[key].forEach(opt => {
                    let optionValue = document.querySelectorAll('[group_option_value_id="' + opt + '"]');
                    if(optionValue.length && optionValue[0].classList.contains('active')) {
                        amountFamilyOptions--;
                    }
                });
                if(amountFamilyOptions === 0) {
                    value = parameter._options.find(opt => {  return opt.key_family === key; }).value;
                    return value;
                }
            });
            return value;
        }

        _addOptionSelectHandler(optionId, handler) {
            let handlers = this._optionSelectHandlers;
            handlers[optionId] || (handlers[optionId] = []);
            handlers[optionId].push(handler);
        }

        // Inform customizer about external option select
        selectOption(optionId, groupId) {
            if (this._selectedOptions[groupId] != optionId) {
                // Save selected option ID
                this._selectedOptions[groupId] = optionId;
                // Call handlers
                (this._optionSelectHandlers[optionId] || []).forEach(function(handler) {
                    handler(optionId);
                });
            }
        }

        unselectOption(optionId, groupId) {
            if (this._selectedOptions[groupId] == optionId) {
                // Remove selected option ID
                this._selectedOptions[groupId] = null;
                // Restore default parameter value
                let parameters = (this._parametersByOptionIds[optionId] || []);
                parameters.forEach(this._restoreParameterDefault, this);
            }
        }

        _restoreParameterDefault(parameter) {
            // Update parent object
            let value = this._parameterDefaults.get(parameter.objectId),
                object = parameter.parent;
            DataHelper.setField(object, parameter.getName(), value);
            // Set event
            let data = {};
            data[parameter.getName()] = value;
            this._context.eventManager.notify('object', 'changed', {
                id: object.objectId,
                parentId: this._context.objectHelper.getParentId(object),
                data: data
            });
        }


        // MeasurementRestrictions

        _initMeasurementRestriction(measurementRestriction) {
            let optionId = (measurementRestriction.optionId || []);

            // Add option select handlers
            this._addOptionSelectHandler(optionId, function() {
                this._onOptionSelectMRestr(measurementRestriction, optionId, measurementRestriction._params);
            }.bind(this));

            let otherOptionIds = $('[group_option_value_id='+optionId+']').siblings().filter(function(ind, element) {
                if($(element).attr('group_option_value_id') !== optionId) return $(element).attr('group_option_value_id');
            }).map(function (ind, el) {
                return $(el).attr('group_option_value_id');
            }).toArray();

            let otherMeasurementRestrictions = measurementRestriction.parent.objectData['measurement_restrictions'].map(function(mr) {
                return mr.optionId;
            });

            let otherMeasurementRestrictionIds = otherOptionIds.filter(function(optId){
                if(otherMeasurementRestrictions.indexOf(optId) < 0 && optId != optionId) return optId;
            }).map(function (optId) {
                return optId;
            });

            otherMeasurementRestrictionIds.forEach(function(otherOptionId){
                this._addOptionSelectHandler(otherOptionId, function() {
                    this._onOptionSelectMRestrClean(measurementRestriction, otherOptionId);
                }.bind(this));
            }.bind(this));
        }

        _onOptionSelectMRestr(measurementRestriction, optionId, optionParams) {

            let block = measurementRestriction.parent;

            ['Width', 'Height'].forEach(function(dimension) {
                let measurement = block.getMeasurementByDimension(dimension.toLowerCase())
                if(measurement) {
                    measurement._measurement.setMinValue(optionParams['min' + dimension]);
                    measurement._measurement.setMaxValue(optionParams['max' + dimension]);
                    measurement._measurement._input._updateMinMax();
                    measurement._measurement._input.checkCurrentValue();
                }
            });
            // Send event
            let data = {};
            data[measurementRestriction._type] = optionParams;
            this._context.eventManager.notify('object', 'changed', {
                id: measurementRestriction.objectId,
                parentId: this._context.objectHelper.getParentId(measurementRestriction),
                data: data
            });
        }

        _onOptionSelectMRestrClean(measurementRestriction, optionId) {
            let block = measurementRestriction.parent;

            ['Width', 'Height'].forEach(function(dimension) {
                let measurement = block.getMeasurementByDimension(dimension.toLowerCase())
                if(measurement) {
                    measurement._measurement.setMinValue(measurement._measurement._input._currentMin);
                    measurement._measurement.setMaxValue(measurement._measurement._input._currentMax);
                    measurement._measurement._input.checkCurrentValue();
                }
            });
            // Send event
            let data = {};
            data[measurementRestriction._type] = null;
            this._context.eventManager.notify('object', 'changed', {
                id: measurementRestriction.objectId,
                parentId: this._context.objectHelper.getParentId(measurementRestriction),
                data: data
            });
        }


        // Measurements

        _initMeasurement(measurement) {
            let paramId = measurement.objectData.param_id;
            if (paramId) {
                this._measurementsByParamId[paramId]
                || (this._measurementsByParamId[paramId] = []);
                this._measurementsByParamId[paramId].push(measurement.getMeasurement());
            }
        }

        _addMeasurementByParamId(paramId, measurement) {
            let map = this._measurementsByParamId;
            map[paramId] || (map[paramId] = []);
            map[paramId].push(measurement);
        }

        getMeasurementValue(paramId) {
            let measurements = (this._measurementsByParamId[paramId] || []);
            return (measurements.length > 0)
                ? (measurements[0].getInputValue() || measurements[0].getValue())
                : null;
        }

        getMeasurementName(paramId) {
            let measurements = (this._measurementsByParamId[paramId] || []);
            return (measurements.length > 0)
                ? (measurements[0].name)
                : null;
        }

        getNamedMeasurements(){
            return this._context.measurementManager.getAllByName();
        }

        getMeasurementMin(paramId) {
            let measurements = (this._measurementsByParamId[paramId] || []);
            return measurements
                .map(measurement => measurement.min)
                .filter(value => value !== null)
                .reduce(
                    (acc, value) => ((acc !== null) ? Math.max(acc, value) : value),
                    null);
        }

        getMeasurementMax(paramId) {
            let measurements = (this._measurementsByParamId[paramId] || []);
            return measurements
                .map(measurement => measurement.max)
                .filter(value => value !== null)
                .reduce(
                    (acc, value) => ((acc !== null) ? Math.min(acc, value) : value),
                    null);
        }

        setMeasurementValue(paramId, value) {
            // Get measurements by paramId
            let measurements = (this._measurementsByParamId[paramId] || []);

            // Set measurement values
            measurements.forEach(function(measurement) {
                measurement.setInputValue(value);
            });

            // Collect block IDs
            let blockIds = new ObjectId.Set();
            measurements.forEach(function(measurement) {
                if (measurement.data
                    && measurement.data.objectId
                    && measurement.data.objectId.type == 'block')
                {
                    blockIds.add(measurement.data.objectId);
                }
            });
            // Update restrictions
            let om = this._context.objectManager;
            blockIds.toArray()
                .map(om.get, om)
                .forEach(this._updateBlockRestrictions, this);

            // Redraw
            this.redraw();
        }

        // Links
        _initLink(link) {
            this._context.linkManager.updateLink(link);
        }

        // Handler is called when measurement value is changed in customizer
        addUpdateMeasurementHandler(handler) {
            this._updateMeasurementHandlers.push(handler);
        }

        // Run callbacks for all measurements (syncing external inputs with customizer)
        updateMeasurements() {
            this._forEachObject(function(object) {
                this._updateBlockMeasurements(object);
            }.bind(this), 'block');
        }

        _updateBlockMeasurements(block) {
            let oh = this._context.objectHelper;
            oh.getChildren(block, 'measurement').forEach(function(measurement) {
                if (measurement.objectData.param_id) {
                    let measurementFront = measurement.getMeasurement();
                    this._updateMeasurement(
                        measurement.objectData.param_id,
                        // measurementFront.getInputValue() || measurementFront.getValue(),
                        measurementFront.getInputValue(),
                        measurementFront.getInputErrorCode());
                }
            }, this);
        }

        // Run measurement callback, pass ext. param ID and value set by customizer
        _updateMeasurement(paramId, value, errorCode) {
            this._updateMeasurementHandlers.forEach(function(handler) {
                handler(paramId, value, errorCode);
            });
        }


        // Restrictions

        addExtOptionToggleHandler(handler) {
            this._extOptionToggleHandlers.push(handler);
        }

        updateRestrictions() {
            this._forEachObject(function(object) {
                this._updateBlockRestrictions(object);
            }.bind(this), 'block');
        }

        // Check restrictions for changed block, run callbacks
        _updateBlockRestrictions(block) {
            let oh = this._context.objectHelper,
                box = block.box;

            function isOptionAvailable(restriction) {
                let minWidth = restriction.params.minWidth,
                    minHeight = restriction.params.minHeight,
                    maxWidth = restriction.params.maxWidth,
                    maxHeight = restriction.params.maxHeight;
                return (!minWidth || minWidth <= box.width)
                    && (!minHeight || minWidth <= box.height)
                    && (!maxWidth || maxWidth >= box.width)
                    && (!maxHeight || maxWidth >= box.height);
            }

            oh.getChildren(block, 'restriction').forEach(function(restriction) {
                if (restriction.optionId) {
                    this._toggleExtOption(
                        restriction.optionId,
                        isOptionAvailable(restriction));
                }
            }, this);
        }

        // Run restriction callback, pass ext. option ID and availability
        _toggleExtOption(optionId, isAvailable) {
            this._extOptionToggleHandlers.forEach(function(handler) {
                handler(optionId, isAvailable);
            });
        }


        // Drawing

        redraw() {
            this._drawer.reset();
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                let object = this._context.objectManager.get(objectId);
                this._prepareRootObject(object);
                this._drawer.draw(object);
            }, this);
        }

        clear() {
            // destroy object tree
            let rootObjectIds = this._context.rootObjectIds,
                om = this._context.objectManager,
                oh = this._context.objectHelper;
            this._forEachObject(function(object) {
                om.destroy(object.objectId);
                rootObjectIds.remove(object.objectId);
            });
            this.redraw();
        }

        resize(width, height) {
            this._drawer.resize(width, height);
            this._adjustScale();
            this.redraw();
        }

        _adjustScale(maximize = false) {
            let om = this._context.objectManager,
                scaleMultiplier = this._context.config.scaleMultiplier; // default scale
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                let object = om.get(objectId);
                this._prepareRootObject(object);

                let rect = object.getBoundingRect(),
                    scaleMax = Math.min(
                        this._canvas.width / rect.width,
                        this._canvas.height / rect.height);
                scaleMax *= 0.9;
                scaleMultiplier = maximize
                    ? Math.min(scaleMultiplier * 2, scaleMax)
                    : Math.min(scaleMultiplier, scaleMax);
            }, this);
            this._drawer.scaleMultiplier = scaleMultiplier;
        }

        _prepareRootObject(object) {
            let measurementManager = this._context.measurementManager;
            object.reset();
            object.prepare();
            object.place();
            measurementManager.collect(object);
            measurementManager.prepare(object);
        }

        // Object tree traversal

        _forEachObject(callback, type = null) {
            let oh = this._context.objectHelper,
                om = this._context.objectManager;
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                let object = om.get(objectId);
                oh.forEach(object, callback, type);
            });
        }

        get context() { return this._context; }
        get drawer() { return this._drawer; }
    }

    return Customizer;
});
