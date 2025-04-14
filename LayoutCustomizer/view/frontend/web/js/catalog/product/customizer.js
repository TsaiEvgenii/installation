define([
    'uiRegistry',
    'underscore',
    'jquery',
    'mage/translate',
    'catalogAddToCart',
    'customizerCanvas',
    './customizer/option-map',
    './customizer/toolbar',
    './customizer/validation'
], function(
    registry,
    _,
    $,
    $t,
    catalogAddToCart,
    Customizer,
    OptionMap,
    Toolbar,
    Validation) {

    let customizerConfigDefault = {
        smoothing: false,
        scale: 1.0,
        background: '#f3f8f8',
        canvas: {
            width: '800',
            height: '600'
        }
    };

    let beforeProductFormSubmitHandlers = [];

    $.widget('layoutCustomizer.layoutCustomizer', {
        options: {
            mwOptionInputSelector: '.product-custom-option[type=radio]',
            mwParamInputSelector: '.product-custom-option[type=text]',
            productFormSelector: '#product_addtocart_form',
            mwOptionMap: {},
            customizerConfig: {},
            customizerAssets: {},
            imageDataInputName: 'generated-image-data',
            errorElementClassName: 'option-error',
            toolbarSelector: null,
            addToCartButtonSelector: '.action.tocart'
        },

        _create: function() {
            // trigger event to enable the Add to cart button [https://app.asana.com/0/1202243638585273/1208432533525322/f]
            if(this.options.mainCanvas) {
                $(this.options.addToCartButtonSelector).prop('disabled', true);
            }

            requestAnimationFrame(this._initCustomizer.bind(this));
        },

        _initCustomizer: function() {
            // Get customizer config
            let customizerConfig = $.extend(
                true, {},
                customizerConfigDefault,
                this.options.customizerConfig);

            // Init customizer
            let rootElement = this.element.get(0),
                customizer = new Customizer(rootElement, customizerConfig);
            _.each(this.options.customizerAssets, function(url, id) {
                customizer.drawer.addAssetUrl(id, url);
            });
            this._customizer = customizer;

            this.element.css('height', '100%');

            this._initSizesCustomizer();

            //for using lazyloading design we hired this events
            $(document).trigger('product-image-built');
            // Import layout data
            registry.get('belvgLayoutConfig', this._initLayoutConfig.bind(this));

            // trigger event to enable the Add to cart button [https://app.asana.com/0/1202243638585273/1208432533525322/f]
            if(this.options.mainCanvas) {
                $(this.options.addToCartButtonSelector).prop('disabled', false);
            }
        },

        _initSizesCustomizer: function() {
            let rootElement = this.element.get(0),
                self = this;
            // Canvas size update
            function updateCanvasSize() {
                if(self.element.is(':visible')){
                    let canvasWrapper = $(rootElement).find('canvas').parent(),
                        canvasWrapperHeight = canvasWrapper.innerHeight(),
                        canvasWrapperWidth = canvasWrapper.innerWidth();

                    let height, width;

                    if(self.options.mainCanvas) {
                        let wrapper = $(rootElement).closest('.product-view-switcher'),
                            wrapperHeight = wrapper.innerHeight() - wrapper.find('.product-view-switcher_tabs').innerHeight(),
                            wrapperWidth  = wrapper.innerWidth();

                        height = wrapperHeight;

                        if(canvasWrapperHeight < wrapperHeight) {
                            height = canvasWrapperHeight;
                        }
                        width = height * 4 / 3;
                        if(width > wrapperWidth) {
                            width = wrapperWidth;
                            height = width / 4 * 3;
                        }
                        // get into account the tooltip height and it's bottom position + top gap
                        height -= (30 + 4 + 4);
                    } else {
                        width = canvasWrapperWidth;
                        height = width / 4 * 3;
                    }
                    self._customizer.resize(width, height);
                }
            }
            $(window).on("resize", updateCanvasSize);

            function respondToVisibility(element, callback) {
                let observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        callback(entry.intersectionRatio > 0);
                    });
                });
                observer.observe(element);
            }

            respondToVisibility(rootElement, visible => {
                if(visible) {
                    updateCanvasSize()
                }
            });
        },

        _initLayoutConfig: function(layoutConfig) {
            // Import layout data
            let blocks = layoutConfig.layout_props.blocks;
            if (blocks) {
                this._customizer.importData(blocks);
            }

            this._initOptions(layoutConfig);
            this._initParams(layoutConfig);
            this._initOptionRestrictions(layoutConfig);
            this._initToolbar(layoutConfig);
            this._applyCustomization(layoutConfig);
            this._initProductForm(layoutConfig);
            this._applySynchronization();

            registry.set('belvgLayoutCustomizerWidget', this);
        },

        _initToolbar: function(layoutConfig) {
            let selector = this.options.toolbarSelector;
            if (selector) {
                let customizer = this._customizer,
                    widthUuid = layoutConfig.overall_width,
                    mwWidthWrapper = this._getMwOptionWrapper(widthUuid),
                    widthParamId = mwWidthWrapper.attr('group_option_id'),
                    heightUuid = layoutConfig.overall_height,
                    mwHeightWrapper = this._getMwOptionWrapper(heightUuid),
                    heightParamId = mwHeightWrapper.attr('group_option_id');
                this._toolbar = $(selector).layoutCustomizerToolbar({
                    layoutConfig: layoutConfig,
                    widthMin: widthParamId ? customizer.getMeasurementMin(widthParamId) : null,
                    widthMax: widthParamId ? customizer.getMeasurementMax(widthParamId) : null,
                    heightMin: heightParamId ? customizer.getMeasurementMin(heightParamId) : null,
                    heightMax: heightParamId ? customizer.getMeasurementMax(heightParamId) : null,
                });
                customizer.updateMeasurements();
            }
        },

        _initOptions: function(layoutConfig /* unused?? */) {
            let self = this,
                customizer = this._customizer,
                inputs = $(this.options.mwOptionInputSelector);
            inputs.each(function() {
                let input = $(this),
                    optionWrapper = input.closest('[mageworx_option_id]'),
                    mwOptionId = optionWrapper['mageworx_option_id'];

                if (input.val()) {
                    // Select option

                    // Get option value wrapper
                    let wrapper = input.closest('[mageworx_group_option_type_id]'),
                        optionId = wrapper.attr('group_option_value_id');
                    if (optionId) {
                        // Option select handler
                        let selectOption = function() {
                            customizer.selectOption(optionId, mwOptionId);
                            self._checkIsOptionDisabled(optionId);
                        };
                        input.on("change", selectOption);
                        // apply by default
                        if (input.is(':checked')) {
                            selectOption();
                        }
                    }

                } else {
                    // Unselect options

                    // Get option wrapper
                    let wrappers = optionWrapper.find('[mageworx_group_option_type_id]');

                    // Get all option IDs inside optionwrapper
                    let optionIds = wrappers
                        .map(function() {
                            let optionId = $(this).attr('group_option_value_id')
                            return optionId;
                        })
                        .get()
                        .filter(function(optionId) {
                            return !!optionId;
                        });

                    // "None" handler
                    // unselect all options
                    let selectOption = function() {
                        optionIds.forEach(function(optionId) {
                            customizer.unselectOption(optionId, mwOptionId);
                        });
                    };
                    input.change(selectOption);
                    // apply by default
                    if (input.is(':checked')) {
                        selectOption();
                    }
                }
            });
        },

        _initParams: function(layoutConfig) {
            let customizer = this._customizer;

            {
                // Options -> customizer

                // MageWorx options
                let inputs = $(this.options.mwParamInputSelector);
                inputs.each(function() {
                    let input = $(this),
                        wrapper = input.closest('[mageworx_option_id]'),
                        paramId = wrapper.attr('group_option_id');
                    if (paramId) {
                        let setMeasurementValue = function() {
                            if (customizer.getMeasurementValue(paramId) != input.val()) {
                                try {
                                    //check all empty values in URL and values that are not typeof Object
                                    //input.val() can be typeof Object when Add to cart button was clicked
                                    if(input.val().trim() === '' ||
                                        (isNaN(Number(input.val())) && typeof JSON.parse(input.val()) != "object"))
                                        throw "Invalid value";
                                    customizer.setMeasurementValue(paramId, input.val());
                                } catch (exception) {
                                    if(customizer.getMeasurementValue(paramId)){
                                        input.val(customizer.getMeasurementValue(paramId));
                                        input.trigger('change');
                                    }else{
                                        console.log(`ParamId= ${paramId} is empty`);
                                        customizer.redraw();
                                    }
                                }
                            }
                        };
                        input.on('change', setMeasurementValue);
                        // input.on('keyup', setMeasurementValue);
                        if (input.val()) {
                            setMeasurementValue();
                        }
                    }
                });
            }

            {
                // Customizer -> options

                let self = this;
                function onMeasurementUpdate(paramId, value, errorCode) {
                    // Set option input value
                    let input = self._getMwOptionInputById(paramId);
                    if (input.val() != value) {
                        input.val(value);
                        input.trigger('change');
                    }
                    // Set error code
                    input.data('error-code', errorCode);
                    input.trigger('error-code-change');
                }
                // Add customizer update handler
                customizer.addUpdateMeasurementHandler(onMeasurementUpdate);
                // customizer.updateMeasurements();
            }
        },

        _initOptionRestrictions: function(layoutConfig) {
            let self = this;
            function onOptionToggle(optionId, isAvailable) {
                let valueWrapper = self._getMwOptionValueWrapperById(optionId),
                    optionInput = valueWrapper.find('input');
                // Disable or enable option
                valueWrapper.toggleClass('disabled', !isAvailable);
                // optionInput.prop('disabled', !isAvailable); // !!
                // Update error message
                self._checkIsOptionDisabled(optionId);
            }

            let customizer = this._customizer;
            customizer.addExtOptionToggleHandler(onOptionToggle);
            customizer.updateRestrictions();
        },

        _initProductForm: function(layoutConfig) {
            let form = this._getForm(),
                customizer = this._customizer,
                rootElement = this.element,
                scrollElement = rootElement;
            if (this.options.toolbarSelector) {
                let toolbarElement = $(this.options.toolbarSelector);
                scrollElement = toolbarElement;
            }

            beforeProductFormSubmitHandlers.push(function(form) {
                // Measurement validation
                if (!customizer.validateMeasurements()) {
                    scrollElement.find('.label.ui-accordion-header').trigger('click');
                    throw 'some measurements are invalid';
                }

                // Customizations
                {
                    let namedMeasurements = customizer.exportNamedMeasurements();
                    this._getSectionSizesInput(layoutConfig)
                        .val(this._paramsToString(namedMeasurements))
                        .trigger('change');
                }

                // Image
                {
                    // Find or create image data input
                    let inputName = this.options.imageDataInputName,
                        input = form.find('input[name=' + inputName + ']');
                    if (input.length == 0) {
                        input = $('<input />');
                        input.attr({type: 'hidden', name: inputName});
                        form.append(input);
                    }

                    // Set image data
                    let imageData = customizer.exportImage();
                    input.val(imageData);
                }
            }.bind(this));

            beforeProductFormSubmitHandlers.push(function(form) {
                $(form).validate();
                if(!$(form).valid()) throw new Error('some errors were found');
            }.bind(this));
        },

        _checkIsOptionDisabled: function(optionId) {
            let valueWrapper = this._getMwOptionValueWrapperById(optionId),
                wrapper = valueWrapper.closest('[mageworx_option_id]'),
                inputs = wrapper.find('input'),
                invalidInput = wrapper.find('.choice.disabled input:checked'),
                errorElement = this._getMwOptionErrorMessageElement(wrapper);
            // reset
            inputs.removeClass('validate-disabled-selected-option')
            errorElement.hide();
            if (invalidInput.length > 0) {
                // Add validation class
                // NOTE: always using first input
                inputs.first().addClass('validate-disabled-selected-option');
                // Show error message
                errorElement
                    .text($t('Selected option is unavailable'))
                    .show();
            }
        },

        _applyCustomization: function(layoutConfig) {
            let input = this._getMwOptionInput(layoutConfig.sections_sizes);
            if (input.val()) {
                this._customizer.importNamedMeasurements(this._paramsFromString(input.val()));
            }
        },

        _applySynchronization: function () {
            const namedMeasurements = this._customizer.getNamedMeasurements();
            let measurementsData = new Proxy({}, {
                set(obj, key, value) {
                    obj[key] = value;
                    let fields = document.querySelectorAll(`[data-measurement-name="${key}"]`);
                    for (let field of fields) {
                        if (field.value !== value) {
                            field.value = value;
                            const event = new Event('change');
                            field.dispatchEvent(event);
                        }
                    }

                    return true;
                }
            });

            for (let name in namedMeasurements) {
                // width and height synchronized in _initParams function
                if (['width', 'height'].includes(name)) {
                    continue;
                }
                let measurementNameSelector = `[data-measurement-name="${name}"]`;
                let measurementInputs = $(measurementNameSelector);
                if (measurementInputs.length > 1) {
                    measurementInputs.each(function () {
                        $(this).on('change', function () {
                            measurementsData[name] = $(this).val()
                        })
                    })

                }
            }
        },

        _getForm: function() {
            return $(this.options.productFormSelector);
        },

        _getSectionSizesInput: function(layoutConfig) {
            return this._getMwOptionInput(layoutConfig.sections_sizes);
        },


        _getMwOptionValueWrapperById: function(optionId) {
            return $('[group_option_value_id=' + optionId + ']');
        },

        _getMwOptionWrapper: function(uuid) {
            let selector = '[mageworx_option_id=' + uuid + ']';
            return $(selector);
        },

        _getMwOptionInput: function(uuid) {
            return this._getMwOptionWrapper(uuid).find('input');
        },

        _getMwOptionInputById: function(paramId) {
            let selector = '[group_option_id=' + paramId + ']';
            return $(selector).find('input');
        },

        _getMwOptionErrorMessageElement: function(wrapper) {
            let title = wrapper.find('label:first .title-cont .text-wrapper'), // ??
                errorElement = title.find('.' + this.options.errorElementClassName);
            if (errorElement.length == 0) {
                errorElement = $(document.createElement('span'))
                    .addClass(this.options.errorElementClassName)
                    .hide();
                title.append(errorElement);
            }
            return errorElement;
        },

        _paramsToString: function(params) {
            return JSON.stringify(params);
        },

        _paramsFromString: function(string) {
            let params = {};
            try {
                params = JSON.parse(string);
            } catch (e) {
                // do nothing
            }
            return params;
        }
    });

    // Extend mage.catalogAddToCart widget,
    // add before submit handlers
    $.widget(
        'mage.catalogAddToCart',
        $.mage.catalogAddToCart,
        {
            submitForm: function(form) {
                try {
                    beforeProductFormSubmitHandlers.forEach(function(handler) {
                        handler(form);
                    });
                    this._super(form);
                } catch (exception) {
                    // do nothing
                }
            }
        });

    return $.layoutCustomizer.layoutCustomizer;
});
