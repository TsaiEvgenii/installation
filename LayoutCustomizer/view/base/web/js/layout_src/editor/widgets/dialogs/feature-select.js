define([
    '../../../blocks/features/all',
    '../../../ui/widgets/dialog/select-list',
], function(FeatureList, SelectList) {

    class FeatureSelect extends SelectList.Widget {
        constructor(context, container = null) {
            super('feature-select-dialog', context, {}, container);

            // Add items
            Object.values(FeatureList).forEach(this._addFeature.bind(this));

            // Set header text
            this.setHeaderText('Select Feature');

            // Add buttons
            this.addOkButton();
            this.addCancelButton();
        }

        _addFeature(feature) {
            this.addItem(feature.Name, feature.Type);
        }
    }

    return {Widget: FeatureSelect};
});
