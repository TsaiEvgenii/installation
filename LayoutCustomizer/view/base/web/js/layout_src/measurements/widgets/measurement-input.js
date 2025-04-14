define([
    '../../ui/widget',
    '../../ui/helper/html',
    './tooltip',
    '../error',
    'jquery',
    '../measurement-dependency-helper'
], function(Widget, HtmlHelper, Tooltip, MeasurementError, $, MeasurementDependencyHelper) {

    let ErrorClass = 'invalid-value';

    function getMinMaxText(measurement) {
        let text = '';
        if (measurement.getMin() !== null) {
            text += 'min ' + measurement.getMin();
        }
        if (measurement.getMax() !== null) {
            text += (text ? ', ' : '') + 'max ' + measurement.getMax();
        }
        return text;
    }

    function preventEnterSubmit(event) {
        if (event.keyCode == 13)
            event.preventDefault();
    }

    function hideMobileMenu() {
        if (window.innerWidth < 375) {
            $('.minicart-wrapper').hide();
            $('.buttons-cont').hide();
        }
    }
    function showMobileMenu() {
        if (window.innerWidth < 375) {
            $('.minicart-wrapper').show();
            $('.buttons-cont').show();
        }
    }

    function checkFeatureSlidingDoors(parentBlock, dimension, value){
        let slidingBlock = parentBlock.getChildWithFeature('sliding-door', 'sliding');
        if(slidingBlock) {
            changeBlockMeasurement(slidingBlock, dimension, value);
        }
    }
    function changeBlockMeasurement(block, dimension, value) {
        let blockMeasurement = block.getMeasurementByDimension(dimension);
        if(blockMeasurement && blockMeasurement._measurement.input) {
            blockMeasurement._measurement.setMaxValue(value);
            blockMeasurement._measurement.input._updateMinMax();
            blockMeasurement._measurement.input._checkCurrentValue();
        }
    }

    // Proportion value calculation
    function getValueByProportion(measurement, obj, dimension) {
        let parentValue = obj.parent.getFullValueByDimension(dimension);
        return measurement._measurement.input ?
            measurement._measurement.input._drawer.format.decimal(measurement._measurement.originalProportion * parentValue) :
            measurement._measurement.originalProportion * parentValue;
    }
    function calculateProportion(value, obj, dimension) {
        let parentValue = obj.parent.getFullValueByDimension(dimension);
        return value / parentValue;
    }

    function copyArray(from) {
        let newArray = [];
        from.forEach(function(obj) {
            newArray.push(obj);
        })
        return newArray;
    }

    function sortArrayByDimension(array, dimension) {
        array.sort(function (a, b) {
            if(a.getMeasurementByDimension(dimension) && b.getMeasurementByDimension(dimension)) {
                a = a.getMeasurementByDimension(dimension).isCustomizable ? 1 : 0;
                b = b.getMeasurementByDimension(dimension).isCustomizable ? 1 : 0;
                if (a > b) return -1;
                if (a < b) return 1;
                return 0;
            }
        });
    }

    function newValueForLinkedSash(obj, om, coefficient, dimension) {
        let oldDimension, newValue, adjusmentSum = 0;
        obj.objectData.links.forEach(function (refObj) {
            if(refObj['_name'] === dimension) {
                let refObject = om.get(refObj._ref);
                if(refObject.getMeasurementByDimension(dimension) && refObject.getMeasurementByDimension(dimension)._measurement.input !== null) {
                    oldDimension = refObject.getMeasurementByDimension(dimension)._measurement.getValue();
                    adjusmentSum = refObject.getMeasurementByDimension(dimension)._getAdjustmentSum();
                    newValue = Number(coefficient * oldDimension);
                    newValue = Number(refObject.getMeasurementByDimension(dimension)._measurement.input._drawer.format.decimal(newValue));
                }
                return false;
            }
        });
        return newValue - adjusmentSum;
    }

    function changeMeasurement (obj, dimension, difference, coefficient, om, flag) {
        let childNewValue = 0, breakPoint = null;
        let otherDimension = dimension === 'width' ? 'height' : 'width';
        if(obj.getValueByDimension(dimension) !== null) {
            // change for linked sashes
            if(obj.objectData.links.length > 0) {
                let oldDimension = obj.getValueByDimension(dimension),
                    newValue = coefficient * oldDimension;
                let newValueAfterFunc = newValueForLinkedSash(obj, om, coefficient, dimension);
                newValue = (newValueAfterFunc) ? newValueAfterFunc : newValue;
                obj.setValueByDimension(dimension, newValue);

                if(obj.hasMeasurementDependency()){
                    breakPoint = obj.getMeasurementDependencyBreakPoint();
                    if(newValue < breakPoint) {
                        let otherMeasurement = obj.getBlocksMeasurementByDimension(otherDimension, om);
                        let otherValue = otherMeasurement._measurement.getValue();
                        if(otherValue < breakPoint) {
                            console.log('linked sash value ERROR!', obj);
                        }
                    }
                }

            } else {
                obj.measurements.forEach(function (measurement) {
                    if (measurement.measurementType === dimension && difference !== 0) {
                        if(flag == 'sibling') {
                            childNewValue = measurement._measurement.input ? measurement._measurement.input._drawer.format.decimal(measurement._measurement.getValue() - difference) : measurement._measurement.getValue() - difference;
                            measurement._measurement.originalProportion = calculateProportion(childNewValue, obj, dimension);
                        } else {
                            if(flag == 'child') {
                                let childOldValue = measurement._measurement.getValue();
                                childNewValue = measurement._measurement.input ? measurement._measurement.input._drawer.format.decimal(coefficient * childOldValue) : coefficient * childOldValue;
                                if(measurement._measurement.originalProportion &&
                                    measurement._measurement.originalProportion !== Infinity) {
                                    childNewValue = getValueByProportion(measurement, obj, dimension);
                                }
                            }
                        }
                        //set min/max value in case if childNewValue > max || childNewValue < min
                        childNewValue = measurement._measurement.setValueMinMax(childNewValue);

                        MeasurementDependencyHelper.checkMeasurementDependency(obj, om, childNewValue, dimension, measurement);

                        //set input value only (without call _onChange)
                        if(measurement._measurement.input) {
                            try {
                                measurement._measurement.input.setValue(childNewValue, false);
                            } catch (e) {
                                console.err(e);
                            }
                            // update linked sash if it exists
                            measurement._measurement.input._context.linkManager.updateRefObjects(measurement.parent.objectId);
                            //check current input value if its min/max was changed
                            measurement._measurement.input._checkCurrentValue();
                        }
                    }
                })
            }
        }
        else {
            if (obj.children.length > 0) {
                obj.children.forEach(function(child) {
                    changeMeasurement (child, dimension, difference, coefficient, om, 'child');
                })
            }
        }

        //check if non-customizable child block has min/max value
        if(obj.getValueByDimension(dimension) === null &&
            obj.getMeasurementByDimension(dimension) &&
            obj.getMeasurementByDimension(dimension)._measurement &&
            flag == 'child') {
            let measurement = obj.getMeasurementByDimension(dimension),
                currentValue = measurement._measurement.getValue(),
                maxValue = measurement._measurement.max ? Number(measurement._measurement.max) : undefined,
                minValue = Number(measurement._measurement.min);

            let newValue = obj.calcNoneMeasurementBlockValue(dimension) + measurement._getAdjustmentSum();
            if(coefficient !== 1) newValue = Number(measurement._measurement.input._drawer.format.decimal(newValue));

            if(measurement && (maxValue || minValue > 0)) {
                if(newValue > maxValue || newValue < minValue) {
                    let rightValue = 0;

                    //get min/max value
                    if (newValue > maxValue) rightValue = maxValue;
                    if (newValue < minValue) rightValue = minValue;

                    //get the difference between values to set it equally to sibling sashes which are customizable
                    let difference = Number(newValue - rightValue),
                        customizableSiblings = obj.parent.getCustomizableChildren(dimension);

                    customizableSiblings.forEach(function (sibling, index) {
                        let siblingMeasurement = sibling.getMeasurementByDimension(dimension)._measurement;
                        if(siblingMeasurement.input) {
                            let siblingValue = Number(siblingMeasurement.input.getValue()),
                                newSiblingValue = siblingMeasurement.input._drawer.format.decimal(siblingValue + difference / customizableSiblings.length);
                            if(siblingMeasurement.originalProportion === undefined) {
                                siblingMeasurement.originalProportion = calculateProportion(siblingValue, sibling, dimension);
                            }
                            try {
                                siblingMeasurement.setValue(newSiblingValue);
                                siblingMeasurement.input.setValue(newSiblingValue, false);
                            } catch (e) {
                                console.error(e);
                            }

                            MeasurementDependencyHelper.checkMeasurementDependency(sibling, om, newSiblingValue, dimension, sibling.getMeasurementByDimension(dimension));
                        }
                    });
                } else {
                    if ((currentValue == maxValue || currentValue == minValue) && newValue !== currentValue) {
                        let customizableSiblings = obj.parent.getCustomizableChildren(dimension);
                        customizableSiblings.forEach(function (sibling, index) {
                            let siblingMeasurement = sibling.getMeasurementByDimension(dimension),
                                newSiblingValue = getValueByProportion(siblingMeasurement, sibling, dimension)

                            try {
                                let valueAfterCheck = siblingMeasurement._measurement.setValueMinMax(newSiblingValue);
                                siblingMeasurement._measurement.input.setValue(valueAfterCheck, false);
                            } catch (e) {
                                console.error(e);
                            }

                            MeasurementDependencyHelper.checkMeasurementDependency(sibling, om, newSiblingValue, dimension, sibling.getMeasurementByDimension(dimension));
                        });
                    }
                }
            }
        }
    }

    class MeasurementInput extends Widget.Base {
        constructor(context, drawer, measurement) {
            super('measurement-input', context, 'div');
            this.element.style.position = 'absolute';

            this._drawer = drawer;
            this._measurement = measurement;
            this._errorCode = null;

            this._initInput();
            this._initTooltip();

            this._currentMin = this._measurement.getMin();
            this._currentMax = this._measurement.getMax();

            // subscribe to all object changes
            this.context.eventManager.subscribe(this, 'object', 'changed');
        }

        onEvent(event) {
            if(event.data.parentId &&
                this._measurement.data.objectId.isSame(event.data.parentId) &&
                event.data.id.type === "feature") {
                if(event.data.data["params.type"] === "sliding") this._changeMax();
                else if(event.data.data["params.type"] === "fixed") this._resetMax();
            }

            this._updateMinMax();
        }

        setValue(value, change = true) {
            if(isNaN(Number(value))) throw "Invalid value";
            this._input.value = value;
            this._input.style.width = ((this._input.value.length + 1) * 15) + 'px';
            if (change)
                this._onChange();
        }

        setValueWithCheck(value) {
            this._input.value = value;
            this._errorCode = this._measurement.setValue(value);

            this._processError(this._errorCode);
        }

        getValue() {
            return this._input.value;
        }

        resetValue() {
            try {
                this.setValue(this._drawer.format.decimal(this._measurement.getValue()));
            } catch (e) {
                console.error(e);
            }
        }

        getErrorCode() {
            return this._errorCode;
        }

        updateMinMax() {
            this._updateMinMax();
        }

        _initInput() {
            let input = this.context.elementFactory.make('input', {
                type: 'text',
                title: getMinMaxText(this._measurement),
                step: 'any'
            }, {'measurementName': this._measurement.name}),
                self = this;

            input.addEventListener('change', function() {
                //mozila fix
                this.value = this.value.replace(',','.');
                if(this.value.match("^[,.]")) {
                    this.value = "0" + this.value;
                }
            });
            input.addEventListener('change', this._onChange.bind(this));
            input.addEventListener('keydown', preventEnterSubmit);
            input.addEventListener('focus', hideMobileMenu);
            input.addEventListener('blur', showMobileMenu);
            input.addEventListener('keyup', function(){
                this.style.width = ((this.value.length + 1) * 15) + 'px';
            });
            input.addEventListener('input', function(e){
                if(!!this.value.trim() && !this.value.match("^[,.]")) {
                    if (this.value.match("^0\\d+"))
                        this.value = this.value.substring(1);
                    else
                        this.value = this.value.match("^\\d+[,.]?\\d{0," + self._drawer.numberPrecision + "}")[0];
                } else if(this.value.match("^[,.]")) {
                    this.value = this.value.match("^[,.]\\d{0,1}")[0];
                }
            });
            input.addEventListener('keypress', function(e){
                let txt = String.fromCharCode(e.which);
                if (txt.match(/[0-9]*\.*/) && !txt.match(/[0-9]*\.*\,*/)[0]) {
                    e.preventDefault();
                    return false;
                }
            });
            input.style.width = ((input.value.length + 1) * 15) + 'px';
            this.element.appendChild(input);
            this._input = input;
        }

        _initTooltip() {
            let tooltip = new Tooltip.Widget(this.context, this._measurement);
            this.element.appendChild(tooltip.element);
            this._tooltip = tooltip;
        }

        checkCurrentValue() {
            this._errorCode = this._measurement.setValue(this._measurement.getValue());
            this._processError(this._errorCode);
        }

        _onChange() {
            // Get value
            let value = (!this._input.value.trim()) ? this._input.value.trim() : Number(this._input.value),
                oldValue = this._measurement.getValue(),
                om = this._context._objectManager,
                dimension = this._context._objectManager.get(this._measurement._hilightObjectId).measurementType,
                difference = value - oldValue,
                coefficient = value/oldValue;

            // Set measurement value
            this._errorCode = this._measurement.setValue(value);

            // Process error code
            this._processError(this._errorCode);

            if(this._errorCode == 0) {
                // Proportional change of child elements
                let parentBlock = this.context.objectManager.get(this._measurement.data.objectId);

                //check if measurement dependency is set
                MeasurementDependencyHelper.checkMeasurementDependency(parentBlock, om, value, dimension, this);

                //check if block has children with feature "sliding-door"
                if(parentBlock.hasSlidingDoors())
                    checkFeatureSlidingDoors(parentBlock, dimension, value/parentBlock.children.length);

                if (parentBlock.parent) {
                    this._measurement.originalProportion = calculateProportion(value, parentBlock, dimension);
                    let firstParent = parentBlock.parent,
                        index = firstParent.getChildIndexByObjectId(this._measurement.data.objectId);
                    if (index >= 0 && firstParent.children.length > (index+1)) {
                        changeMeasurement(firstParent.children[index+1], dimension, difference, coefficient, om, 'sibling');
                    }
                } else {
                    //make array copy to sort it according customizable measurements
                    //to check min/max values of non-customizable blocks
                    let childrenCopy = copyArray(parentBlock.children);
                    sortArrayByDimension(childrenCopy, dimension);
                    //change measurements of customizable blocks at first
                    childrenCopy.forEach(function(child) {
                        changeMeasurement(child, dimension, difference, coefficient, om,'child');
                    });

                    // for blocks with "triangle" shapes and their measurements
                    if(parentBlock.shape._measurements && dimension) {
                        let shapeMeasurements = parentBlock.shape.getMeasurementByDimension(dimension);
                        shapeMeasurements.forEach(function(shapeMeasurement) {
                            if(shapeMeasurement.input) {
                                let oldShapeValue = shapeMeasurement.getValue(),
                                    newShapeValue = shapeMeasurement.input._drawer.format.decimal(oldShapeValue * coefficient);
                                try {
                                    shapeMeasurement.setValue(newShapeValue);
                                    shapeMeasurement.input.setValue(newShapeValue, false);
                                    shapeMeasurement.input.trigger('change');
                                } catch (e) {
                                    console.error(e);
                                }
                            }
                        })
                    }
                }
            }

            // Send event
            this.context.eventManager.notify('object', 'changed', {
                id: this._measurement.data.objectId
            });
        }

        getLinkedObjects(dimension, objectID) {
            let rootObjId = this._context.rootObjectIds._list[0],
                rootObj = this._context._objectManager.get(rootObjId),
                linkedObjects = [];

            rootObj.getAllLinkedObjects(linkedObjects, objectID, dimension);

            return linkedObjects;
        }

        _updateMinMax() {
            let min = this._measurement.getMin(),
                max = this._measurement.getMax();
            // if (min != this._currentMin || max != this._currentMax) {
            // if (min != this._currentMin || max != this._currentMax || max === null) {
                // update input title
                this._input.title = getMinMaxText(this._measurement);
                // try to re-set value from input
                // TODO: check if value can be changed after min/max update
            // }
        }

        _processError(errorCode) {
            switch (this._errorCode) {
            case MeasurementError.ValueIsTooSmall:
                this._tooltip.unhighlightInvalidType();
                this._tooltip.highlightMin();
                HtmlHelper.addClassName(this.element, ErrorClass);
                break;
            case MeasurementError.ValueIsTooLarge:
                this._tooltip.unhighlightInvalidType();
                this._tooltip.highlightMax();
                HtmlHelper.addClassName(this.element, ErrorClass);
                break;
            case MeasurementError.ValueIsInvalid:
                this._tooltip.highlightInvalidType();
                HtmlHelper.addClassName(this.element, ErrorClass);
                break;
            default:
                // MeasurementError.Ok
                this._tooltip.unhighlight();
                this._tooltip.unhighlightInvalidType();
                HtmlHelper.removeClassName(this.element, ErrorClass);
            }
        }

        _changeMax() {
            let parentBlock = this._measurement.parent.parent,
                mainBlock = parentBlock.parent;
            if(mainBlock) {
                 let mainBlockMeasurement = mainBlock.getMeasurementByDimension(this._measurement.parent.measurementType);
                 changeBlockMeasurement(parentBlock, this._measurement.parent.measurementType, mainBlockMeasurement.getValue()/mainBlock.children.length);
            }
        }

        _resetMax() {
            let parentBlock = this._measurement.parent.parent;
            if(parentBlock) {
                changeBlockMeasurement(parentBlock, this._measurement.parent.measurementType, this._currentMax);
            }
        }


        _checkCurrentValue() {
            this._errorCode = this._measurement.checkValue(this.getValue());
            this._processError(this._errorCode);
        }
    }

    return {Widget: MeasurementInput};
});
