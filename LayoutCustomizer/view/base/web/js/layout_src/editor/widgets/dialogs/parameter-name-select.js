define([
    '../../../ui/widgets/dialog/select-list'
], function(SelectList) {

    class ParameterNameSelect extends SelectList.Widget {
        constructor(context, object, container = null) {
            super('parameter-name-select', context, {}, container);

            // Add param items
            let params = this.context.objectHelper.getParams(object);
            for (let name in params) {
                let label = params[name].label || name;
                this.addItem(label, name);
            }

            // Set header text
            this.setHeaderText('Select parameter');

            // Add buttons
            this.addOkButton();
            this.addCancelButton();
        }

        getValue() {
            let name = this._value;
            return name ? {name: name} : null;
        }
    }

    return {Widget: ParameterNameSelect};
})
