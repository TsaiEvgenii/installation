define([
    '../../../blocks/measurements/all',
    '../../../ui/widgets/dialog/select-list',
], function(MeasurementList, SelectList) {

    class MeasurementSelect extends SelectList.Widget {
        constructor(context, container = null) {
            super('measurement-select-dialog', context, {}, container);

            // Add items
            Object.values(MeasurementList).forEach(this._addMeasurement.bind(this));

            // Set header text
            this.setHeaderText('Select Measurement');

            // Add buttons
            this.addOkButton();
            this.addCancelButton();
        }

        _addMeasurement(measurement) {
            this.addItem(measurement.Name, measurement.Type);
        }
    }

    return {Widget: MeasurementSelect};
});
