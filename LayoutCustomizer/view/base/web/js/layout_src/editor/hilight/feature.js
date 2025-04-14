define([
    './features/all'
], function(FeatureList) {

    function hilight(drawer, object) {
        let type = object.featureType,
            helper = FeatureList[type];
        if (helper) {
            helper.hilight(drawer, object);
        }
    }

    return {hilight: hilight};
});
