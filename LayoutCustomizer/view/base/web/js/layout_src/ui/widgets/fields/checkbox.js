define([
    '../field'
], function(Field) {

    class Checkbox extends Field.InputBase {
        constructor(context, params) {
            super('checkbox', context, 'checkbox', params);
        }

        init() {
            this._input.onclick = this.change.bind(this);
        }

        reset() {
            this._input.checked = false;
        }

        getValue() { return this._input.checked; }
        setValue(value) { this._input.checked = !!value; }
    }

    return {Widget: Checkbox};
});
