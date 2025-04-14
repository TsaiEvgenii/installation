define([
    '../field',
    '../../../data/helper'
], function(Field, DataHelper) {

    class Select extends Field.Base {
        constructor(context, params) {
            super('select', context, params, 'div');
            let ef = this.context.elementFactory,
                attributes = Object.assign(
                    {id: true},
                    params.select || {});
            // add select element
            this._input = ef.make('select', attributes);
            this._element.appendChild(this._input);
            // add options
            this._optionValues = [];
            (params.options || []).forEach(this.addOption.bind(this));
            // add onchange handler
            this._input.onchange = this.change.bind(this);
        }

        addOption(option) {
            // add option value
            let idx = this._optionValues.length;
            this._optionValues[idx] = (option.value || null);
            // add option element
            let element = this.context.elementFactory.make('option', {
                value: idx,
                textContent: option.name
            });
            this._input.appendChild(element);
        }

        hasValue(value) {
            return this._getValueOptionIdx(value) != -1;
        }

        getValue() {
            let idx = Number(this._input.value);
            return this._optionValues[idx];
        }

        setValue(value) {
            let idx = this._getValueOptionIdx(value);
            if (idx != -1) {
                this._input.value = idx;
            }
        }

        _getValueOptionIdx(value) {
            return this._optionValues.findIndex(DataHelper.equal.bind(null, value));
        }
    }

    return {Widget: Select};
});
