define([
    '../../../ui/widgets/dialog/select-list'
], function(SelectList) {

    class LinkNameSelect extends SelectList.Widget {
        constructor(context, object, container = null) {
            super('link-name-select', context, {}, container);

            // Add items
            this.addItem('Width', 'width');
            this.addItem('Height', 'height');

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

    return {Widget: LinkNameSelect};
});
