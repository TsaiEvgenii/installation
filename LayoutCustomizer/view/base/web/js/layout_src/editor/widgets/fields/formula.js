define([
    '../../../ui/widgets/field',
        '../../../object/id',
    '../dialogs/object-select'
], function(Field, ObjectId, ObjectSelectDialog) {

    function wrap(objectIdStr) {
        return '${' + objectIdStr + '}';
    }

    class Formula extends Field.NullableInputBase {
        constructor(context, params) {
            super('formula', context, 'text', params);

            // let ef = context.elementFactory;
            // this._addButton = ef.make('button', {
            //     type: 'button',
            //     textContent: 'Insert...',
            //     onclick: this.onAddClick.bind(this)
            // });
            // this.element.appendChild(this._addButton);
        }

        onAddClick() {
            let dialog = new ObjectSelectDialog.Widget(
                this.context,
                {
                    allowMultiple: false,
                    objectType: this.params.objectType
                },
                this.element);
            dialog.onOk = function() {
                this.setValue('' + this.getValue() + wrap(dialog.getValue()));
            }.bind(this);
            this.add(dialog);
            dialog.center();
            dialog.show();
        }
    }

    return {Widget: Formula};
});
