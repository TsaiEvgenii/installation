define([
    '../../../ui/widgets/field',
    '../../../object/id',
    '../dialogs/object-select'
], function(Field, ObjectId, ObjectSelectDialog) {

    class ObjectSelect extends Field.InputBase {
        constructor(context, params) {
            super('object-select', context, 'hidden', params);
            // add label
            this._label = this._makeLabel(params.label || {});
            this.element.appendChild(this._label);
            // add select button
            this._selectButton = this._makeSelectButton(params.selectButton || {});
            this.element.appendChild(this._selectButton);
        }

        getValue() {
            let value = super.getValue();
            return value ? ObjectId.Id.fromString(value) : null;
        }

        setValue(objectId) {
            let value = objectId ? objectId.toString() : '';
            this._label.textContent = value;
            super.setValue(value);
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
            let dialog = new ObjectSelectDialog.Widget(
                this.context,
                {
                    allowMultiple: false,
                    objectType: this.params.objectType
                },
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

    return {Widget: ObjectSelect};
});
