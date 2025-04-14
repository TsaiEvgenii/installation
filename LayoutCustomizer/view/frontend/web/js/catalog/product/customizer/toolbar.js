define([
    'uiRegistry',
    'jquery',
    'customizerError',
    'jquery-ui-modules/widget'
], function (registry, $, MeasurementError) {

    $.widget('layoutCustomizer.layoutCustomizerToolbar', {
        options: {
            layoutConfig: null,
            widthInputSelector: '[name=width]',
            heightInputSelector: '[name=height]',
            fieldWrapperSelector: '.field',
            fieldMinSelector: '.min em',
            fieldMaxSelector: '.max em',
            wrapperErrorClass: 'error',
            boundErrorClass: 'error',
            widthMin: null,
            widthMax: null,
            heightMin: null,
            heightMax: null
        },

        _create: function () {
            $(document).ready(this._initElements.bind(this));
        },

        _initElements: function () {
            let widthInput = this._getWidthInput(),
                heightInput = this._getHeightInput(),
                mwWidthInput = this._getMwWidthInput(),
                mwHeightInput = this._getMwHeightInput();

            // Prevent submit on enter
            function preventSubmitEnter(event) {
                if (event.keyCode == 13)
                    event.preventDefault();
            }

            [widthInput, heightInput].forEach(function(element) {
                element.on('keypress', function(e) {
                    let txt = String.fromCharCode(e.which);
                    if (txt && !txt.match(/[0-9]*[,.]*/)[0]) {
                        e.preventDefault();
                        return false;
                    }
                });
                element.on('input', function() {
                    if(!!this.value.trim() && !this.value.match("^[,.]"))
                        if(this.value.match("^0\\d+"))
                            this.value = this.value.substring(1);
                        else
                            this.value = this.value.match("^\\d+[,.]?\\d{0,1}")[0];
                    else if(this.value.match("^[,.]")) {
                        this.value = this.value.match("^[,.]\\d{0,1}")[0];
                    }
                });
                element.on('change', function() {
                    this.value = this.value.replace(',','.');
                    if(!this.value.trim()) {
                        let mwField = (this.id === 'height') ? mwHeightInput : mwWidthInput;
                        makeShowErrorHandler(mwField, this);
                    }
                    if(this.value.match("^[.,]")) {
                        this.value = "0" + this.value;
                    }
                });
                element.on('keypress', preventSubmitEnter);
            })

            // Copy value (mw input <-> toolbar input)
            function makeCopyHandler(from, to, triggerEvents) {
                return function () {
                    if (to.val() != from.val()) {
                        to.val(from.val());
                        if (triggerEvents) {
                            to.trigger('change');
                            // to.trigger('keyup');
                        }
                    }
                }
            }

            // Error highlight
            let boundErrorClass = this.options.boundErrorClass,
                wrapperErrorClass = this.options.wrapperErrorClass,
                getMinElement = this._getFieldMin.bind(this),
                getMaxElement = this._getFieldMax.bind(this);

            function makeShowErrorHandler(mwInput, input) {
                return function () {
                    // Highligh/unhighlight min or max value,
                    // add/remove error class to input wrapper and disable/enable submit button
                    let minElement = getMinElement(input).parent(),
                        maxElement = getMaxElement(input).parent();
                    input.parent().removeClass(wrapperErrorClass);
                    minElement.removeClass(boundErrorClass);
                    maxElement.removeClass(boundErrorClass);

                    switch (mwInput.data('error-code')) {
                        case MeasurementError.ValueIsTooSmall:
                            input.parent().addClass(wrapperErrorClass);
                            minElement.addClass(boundErrorClass);
                            break;
                        case MeasurementError.ValueIsTooLarge:
                            input.parent().addClass(wrapperErrorClass);
                            maxElement.addClass(boundErrorClass);
                            break;
                        case MeasurementError.ValueIsInvalid:
                            input.parent().addClass(wrapperErrorClass);
                            break;
                    }
                }
            }

            // Width
            if (mwWidthInput.length) {
                // toolbar -> options
                // widthInput.on('keyup', makeCopyHandler(widthInput, mwWidthInput, true));
                widthInput.on('change', makeCopyHandler(widthInput, mwWidthInput, true));

                // options -> toolbar
                mwWidthInput.on('change', makeCopyHandler(mwWidthInput, widthInput));
                mwWidthInput.on('error-code-change', makeShowErrorHandler(mwWidthInput, widthInput));
                widthInput.val(mwWidthInput.val());

                // Min/Max text
                if (this.options.widthMin) {
                    this._getFieldMin(widthInput).text(this.options.widthMin);
                }
                if (this.options.widthMax) {
                    this._getFieldMax(widthInput).text(this.options.widthMax);
                }
            }

            // Height
            if (mwHeightInput.length) {
                // toobar -> options
                // heightInput.on('keyup', makeCopyHandler(heightInput, mwHeightInput, true));
                heightInput.on('change', makeCopyHandler(heightInput, mwHeightInput, true));

                // options -> toolbar
                mwHeightInput.on('change', makeCopyHandler(mwHeightInput, heightInput));
                mwHeightInput.on('error-code-change', makeShowErrorHandler(mwHeightInput, heightInput));
                heightInput.val(mwHeightInput.val());

                // Min/Max text
                if (this.options.heightMin) {
                    this._getFieldMin(heightInput).text(this.options.heightMin);
                }
                if (this.options.heightMax) {
                    this._getFieldMax(heightInput).text(this.options.heightMax);
                }
            }
        },

        _getWidthInput: function () {
            return this.element.find(this.options.widthInputSelector);
        },

        _getHeightInput: function () {
            return this.element.find(this.options.heightInputSelector)
        },

        _getFieldWrapper: function (input) {
            return input.closest(this.options.fieldWrapperSelector);
        },

        _getFieldMin: function (input) {
            return this._getFieldWrapper(input).find(this.options.fieldMinSelector);
        },

        _getFieldMax: function (input) {
            return this._getFieldWrapper(input).find(this.options.fieldMaxSelector);
        },

        _getMwWidthInput: function () {
            let uuid = this.options.layoutConfig.overall_width;
            return uuid ? this._getMwOptionInput(uuid) : null;
        },

        _getMwHeightInput: function () {
            let uuid = this.options.layoutConfig.overall_height;
            return uuid ? this._getMwOptionInput(uuid) : null;
        },

        _getMwOptionInput: function (uuid) {
            let selector = '[mageworx_option_id=' + uuid + ']';
            return $(selector).find('input');
        },

    });

    return $.layoutCustomizer.layoutCustomizerToolbar;
});
