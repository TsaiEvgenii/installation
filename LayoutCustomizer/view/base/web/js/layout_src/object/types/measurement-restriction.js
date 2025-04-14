define([
    '../additional/measurement-restrictions/all'
], function(MeasurementRestrictionList) {

    return {
        name: 'Measurement Restriction',
        additional: true,
        getSubtypeByParent: function(object) { return object.objectId.type; },
        getSubtype: function(measurementRestriction) { return measurementRestriction.restrictionType; },
        getSubtypeClass: function(subtype) { return MeasurementRestrictionList[subtype]; },
        getParent: function(measurementRestriction) { return measurementRestriction.parent; },
        children: {}
    }
});
