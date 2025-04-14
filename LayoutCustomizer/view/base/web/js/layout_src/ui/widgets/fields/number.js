define([
    '../field'
], function(Field) {

    class NumberInput extends Field.NullableInputBase {
        constructor(context, params) {
            // add step="any" to allow decimals
            params.input || (params.input = {});
            if (params.input.step === undefined) {
                params.input.step = "any";
            }
            super('number', context, 'number', params);
        }

        getValue() {
            let value = super.getValue();
            if (value !== null) {
                value = Number(value);
                if (isNaN(value)) {
                    return this.params.isNullable ? null : 0;
                }
                return value;
            }
            return null;
        }
    }

    return {Widget: NumberInput};
});
