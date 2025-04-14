define([
    '../measurement-restriction'
], function(MeasurementRestriction) {

    class BlockMeasurementRestriction extends MeasurementRestriction.Base {
        constructor() {
            super('block');
            this.params.minWidth = null;
            this.params.minHeight = null;
            this.params.maxWidth = null;
            this.params.maxHeight = null;
        }
    }

    return BlockMeasurementRestriction;
});
