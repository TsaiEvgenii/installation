define([
    '../../../ui/widgets/field',
    '../dialogs/ext-value-select',
    '../../helper/ext-values'
], function(Field, ExtValueSelectDialog, ExtValueHelper) {

    class ExtValueSelect extends Field.InputBase {
        constructor(context, params, extValues) {
            super('ext-option-select', context, 'hidden', params);
            // add label
            this._label = this._makeLabel(params.label || {});
            this.element.appendChild(this._label)
            // add select button
            this._selectButton = this._makeSelectButton(params.selectButton || {});
            this.element.appendChild(this._selectButton);
            // ext. values
            this._extValues = extValues;
        }

        setValue(value) {
            let extOption = ExtValueHelper.get(this._extValues, value);
            if (extOption) {
                this._label.textContent = extOption.label;
                this._label.title = extOption.path;
                super.setValue(extOption.id);
            }
        }

        _makeLabel(attributes) {
            return this.context.elementFactory.make('label', attributes);
        }

        _makeSelectButton(attributes) {
            return this.context.elementFactory.make(
                'button',
                Object.assign(attributes, {
                    type: 'button',
                    textContent: 'Select',
                    onclick: this._selectValue.bind(this)
                }));
        }

        _selectValue() {
            let dialog = new ExtValueSelectDialog.Widget(
                this.context,
                {allowMultiple: false},
                this._extValues,
                this.element);
            dialog.onOk = function() {
                this.setValue(dialog.getValue());
                this.change();
            }.bind(this);
            this.add(dialog);
            dialog.center();
            dialog.show();
        }
    }

    return {Widget: ExtValueSelect};
});
