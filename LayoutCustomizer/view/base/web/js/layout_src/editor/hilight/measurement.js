define([
    './measurements/all'
], function(MeasurementList) {

    function hilight(drawer, object) {
        let type = object.measurementType,
            helper = MeasurementList[type];
        if (helper) {
            helper.hilight(drawer, object);
        }
    }

    return {hilight: hilight};
});
