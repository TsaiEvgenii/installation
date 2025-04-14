define([
    '../../blocks/features/all',
    '../additional/parameter'
], function(FeatureList, Parameter) {

    return {
        name: 'Feature',
        getSubtype: function(feature) { return feature.featureType; },
        getSubtypeClass: function(subtype) { return FeatureList[subtype].Feature; },
        getParent: function(feature) { return feature.parent; },
        getParams: function(feature) { return FeatureList[feature.featureType].Params; },
        children: {
            parameter: {
                subtypes: ['feature'],
                canAddChild: function(feature, parameter) {
                    return !parameter.parent
                        || (parameter.parent.featureType == feature.featureType);
                },
                list: Parameter.getObjectList,
                add: Parameter.addToObject,
                insert: Parameter.insertIntoObject,
                remove: Parameter.removeFromObject,
                position: Parameter.positionInObject
            }
        }
    };
});
