define([
    '../../../ui/widgets/field',
    '../dialogs/ext-value-select',
    '../../../ui/widgets/fields/option-list',
    '../../../data/helper',
    '../../helper/ext-values'
], function(Field, ExtValueSelectDialog, OptionList, DataHelper, ExtValueHelper) {

    class ExtOptionList extends OptionList.Widget {
        constructor(context, params) {
            super(context, params);
            this._extOptions = context.config.ExtOptions || [];
        }

        setValue(value) {
            // Extend with external option data
            value.sort((a,b) => (Number(a.key_family) > Number(b.key_family)) ? 1 : ((Number(b.key_family) > Number(a.key_family)) ? -1 : 0));
            value.forEach(function(a, ind){
                if(a.key_family && a.parent_key_family === null) {
                    let indexF = value.map(e => e.parent_key_family).indexOf(a.key_family);
                    if(ind+1 <= indexF) {
                        value.splice(ind, 0, value[indexF]);
                        value.splice(indexF+1,1);
                    }
                }
            });
            value = value.map(function(option) {
                let extOption = ExtValueHelper.get(this._extOptions, option.id);
                if(option.key_family){
                    extOption = DataHelper.merged(extOption, {key_family: option.key_family});
                }
                if(option.parent_key_family) {
                    extOption = DataHelper.merged(extOption, {parent_key_family: option.parent_key_family});
                }
                return extOption
                    ? DataHelper.merged(extOption, {value: option.value})
                    : null;
            }, this);
            // Remove not found options
            value = value.filter(function(option) { return !!option; });

            super.setValue(value);
        }

        _addNewOption() {
            let dialog = new ExtValueSelectDialog.Widget(
                this.context,
                {allowMultiple: true},
                this._extOptions,
                this._addButton.parentNode);
            dialog.onOk = function() {
                dialog.getValues().forEach(this._addNewExtOption, this);
            }.bind(this);
            this.add(dialog);
            dialog.center();
            dialog.show();
        }

        _addOneOption(row) {
            this._currentRow = row;
            let dialog = new ExtValueSelectDialog.Widget(
                this.context,
                {allowMultiple: true},
                this._extOptions,
                row);
            dialog.onOk = function() {
                dialog.getValues().forEach(this._addOneMoreExtOption, this);
            }.bind(this);
            this.add(dialog);
            dialog.center();
            dialog.show();
        }
        _addOneMoreExtOption(extOptionId) {
            let extOption = ExtValueHelper.get(this._extOptions, extOptionId);
            if (extOption) {
                let option = DataHelper.merged(extOption, {value: ''});

                this._addOneMoreOption(option, this._currentRow);

            }

        }

        _addNewExtOption(extOptionId) {
            let extOption = ExtValueHelper.get(this._extOptions, extOptionId);
            if (extOption) {
                let option = DataHelper.merged(extOption, {value: ''});
                this._addOption(option);
            }

        }

        _makeValueColumn(row) {
            let ef = this.context.elementFactory,
                col = ef.make('td'),
                extIdInput = ef.make('input', {type: 'hidden'}),
                valueInput = this._makeValueInput();
            valueInput.onchange = function() {
                if(this._params.valueInput == 'checkbox'){
                    this._changeRow(row, 'value', valueInput.checked);
                } else {
                    this._changeRow(row, 'value', valueInput.value);
                }
            }.bind(this)
            col.appendChild(extIdInput);
            col.appendChild(valueInput);
            return col;
        }

        _makeValueInput() {
            let ef = this.context.elementFactory,
                type = this._params.valueInput || 'text',
                input = null;
            if (type == 'select') {
                input = ef.make('select');
                (this._params.valueOptions || []).forEach(function(option) {
                    input.appendChild(ef.make('option', {
                        textContent: option.name || '',
                        value: option.value || ''
                    }));
                });
            } else {
                input = ef.make('input', {type: type});
            }
            return input;
        }

        _makeLabelColumn(row) {
            return this.context.elementFactory.make('td');
        }

        // TODO: refactoring (this and parent classes)
        _updateRow(row, option) {
            let inputs = row.getElementsByTagName('input'),
                selects = row.getElementsByTagName('select'),
                extIdInput = inputs[0],
                valueInput = (this._params.valueInput == 'select' ? selects[0] : inputs[1]),
                labelCol = row.getElementsByTagName('td')[2];
            // extId
            if(extIdInput)
                extIdInput.value = option.id;
            // value
            if(this._params.valueInput == 'checkbox') {
                valueInput.checked = option.value;
            } else {
                if(valueInput)
                    valueInput.value = option.value;
            }
            // label
            labelCol.textContent = option.label;
            labelCol.title = option.path;
        }
    }

    return {Widget: ExtOptionList};
});
