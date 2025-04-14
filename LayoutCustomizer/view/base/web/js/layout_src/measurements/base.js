define([
    '../blocks/drawable',
    './error'
], function(Drawable, Error) {
    class Base extends Drawable.Base {
        constructor(measurementType, getter, setter) {
            super('measurement');
            this._measurementType = measurementType;
            this._getter = getter;
            this._setter = setter;
            this._name = null;
            this._data = {};
            this._isCustomizable = false;
            this._min = 0;
            this._max = null;
            this._hilightObjectId = null;
            this._input = null;
            this._formulaEvaluator = null;
            this._validator = null;
        }

        checkValue(value) {
            let min = this.getMin(),
                max = this.getMax();

            let errorCode = Error.Ok;
            if (min !== null && value < min) {
                errorCode = Error.ValueIsTooSmall;
            } else if (max !== null && value > max) {
                errorCode = Error.ValueIsTooLarge;
            }

            if (min === null && value < 0) {
                errorCode = Error.ValueIsTooSmall;
            }

            return errorCode;
        }

        getValue() {
            return this._getter ? this._getter() : null;
        }

        // returns error code
        setValue(value) {
            let min = this.getMin(),
                max = this.getMax(),
                oldValue = this.getValue();

            // Check and adjust value
            let errorCode = Error.Ok;
            if(isNaN(value) || value === undefined || value === 0 || value === null || value === "") {
                value = value.toString().trim() == 0 ? value : oldValue;
                errorCode = Error.ValueIsInvalid;
            } else {
                value = this._input._drawer.format.toDecimal(value);
                if (min !== null && value < min) {
                    value = oldValue;
                    errorCode = Error.ValueIsTooSmall;

                } else if (max != null && value > max) {
                    value = oldValue;
                    errorCode = Error.ValueIsTooLarge;
                }

                if (min === null &&  value < 0) {
                    value = 1;
                    errorCode = Error.ValueIsTooSmall;
                }
            }

            // Set value
            if (this._setter && this.isCustomizable) {
                this._setter(value);
            }

            return errorCode;
        }

        setValueMinMax(value) {
            let min = this.getMin(),
                max = this.getMax();
            if (min !== null && value < min) value = min;
            else if (max !== null && value > max) value = max;
            if (min === null && value < 0) value = 0;
            // Set value
            if (this._setter && this.isCustomizable) {
                this._setter(value);
            }
            return value;
        }

        getInputValue() {
            return this._input ? this._input.getValue() : null;
        }

        getInputErrorCode() {
            return this._input ? this._input.getErrorCode() : null;
        }

        setInputValue(value) {
            if (this._input) {
                this._input.setValue(value);
            }
        }

        setMaxValue(value) {
            this.max = value;
            this.parent.setMaxParam(value);
        }

        getMin() {
            let values = [
                // measurement limit
                (this._min !== null) ? this.evalLimit(this._min) : null,
                // validator limit
                this._validator ? this._validator.getMin() : null
            ].filter(v => v !== null);

            return (values.length > 0)
                ? Math.max.apply(null, values)
                : null;
        }

        getMax() {
            let values = [
                // measurement limit
                (this._max !== null) ? this.evalLimit(this._max) : null,
                // validator limit
                this._validator ? this._validator.getMax() : null
            ].filter(v => v !== null);

            return (values.length > 0)
                ? Math.min.apply(null, values)
                : null;
        }

        evalLimit(expr) {
            return this.formulaEvaluator.evaluate(expr, {measurement: this});
            return this.formulaEvaluator
                ? this.formulaEvaluator.evaluate(expr, {measurement: this})
                : null;
        }

        _updateInputMinMax() {
            if (this._input) {
                this._input.updateMinMax();
            }
        }

        setMinValue(value) {
                this.min = value;
                this.parent.params.min = value;
        }

        setMaxValue(value) {
                this.max = value;
                this.parent.params.max = value;
        }

        get data() { return this._data; }

        get measurementType() {
            return this._measurementType;
        }

        get name() { return this._name; }
        set name(name) { this._name = name; }

        get isCustomizable() { return this._isCustomizable; }
        set isCustomizable(isCustomizable) { this._isCustomizable = isCustomizable; }

        get min() { return this._min; }
        set min(min) { this._min = min; }

        get max() { return this._max; }
        set max(max) { this._max = max; }

        get hilightObjectId() { return this._hilightObjectId; }
        set hilightObjectId(objectId) { this._hilightObjectId = objectId; }

        get input() { return this._input; }
        set input(input) { this._input = input; }

        get formulaEvaluator() { return this._formulaEvaluator; }
        set formulaEvaluator(formulaEvaluator) {
            this._formulaEvaluator = formulaEvaluator;
            this._updateInputMinMax();
        }

        get validator() { return this._validator; }
        set validator(validator) {
            this._validator = validator;
            this._updateInputMinMax();
        }
    }

    return Base;
});
