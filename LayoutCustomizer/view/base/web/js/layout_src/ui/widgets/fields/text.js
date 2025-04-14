define([
    '../field'
], function (Field) {

    class TextInput extends Field.NullableInputBase {
        constructor(context, params) {
            super('text', context, 'text', params);
        }
    }

    return {Widget: TextInput};
});
