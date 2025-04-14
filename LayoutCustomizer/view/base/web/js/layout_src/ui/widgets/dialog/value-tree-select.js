define([
    '../dialog',
    '../value-tree',
    '../../helper/html'
], function(Dialog, ValueTree, HtmlHelper) {

    class ValueTreeSelect extends Dialog.Base {
        constructor(context, params, values, container = null) {
            super('value-select-dialog', context, container);

            this.setHeaderText('Select Value');

            // Add tree widget
            this._tree = new ValueTree.Widget(context, params, values);
            this.add(this._tree);
            this.body.appendChild(this._tree.element);

            this.addOkButton();
            this.addCancelButton();
        }

        getValues() {
            return this._tree.getValues();
        }

        getValue() {
            return this._tree.getValue();
        }
    }

    return {Widget: ValueTreeSelect};
});
