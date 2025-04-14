define([
    '../field'
], function (Field) {

    class ColorInput extends Field.NullableInputBase {
        constructor(concolor, params) {
            super('color', concolor, 'color', params);
        }
    }

    return {Widget: ColorInput};
});
