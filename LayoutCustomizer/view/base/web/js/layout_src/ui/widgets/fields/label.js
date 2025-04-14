define([
    '../field'
], function(Field) {

    // Params:
    // - nullText

    class Label extends Field.Base {
        constructor(context, params) {
            super('label', context, {}, 'span', {id: true});
            this._value = null;
            this._input = this.element;
            this._nullText = (params.nullText !== undefined) ? params.nullText : '';
        }

        getValue() { return this._value; }

        setValue(value) {
            this._value = value;
            this.element.textContent = (value !== null)
                ? value.toString()
                : this._nullText;
        }
    }

    return {Widget: Label};
});
