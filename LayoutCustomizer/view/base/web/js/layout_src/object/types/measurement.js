define([
    '../../blocks/measurements/all'
], function(MeasurementList) {

    return {
        name: 'Measurement',
        getSubtype: function(measurement) { return measurement.measurementType; },
        getSubtypeClass: function(subtype) { return MeasurementList[subtype].Measurement; },
        getParent: function(measurement) { return measurement.parent; },
        getParams: function(measurement) {
            return MeasurementList[measurement.measurementType].Params; },
        children: {}
    };
})
