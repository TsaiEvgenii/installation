define([
    '../additional/parameters/all'
], function(ParameterList) {

    return {
        name: 'Parameter',
        additional: true,
        getSubtypeByParent: function(object) { return object.objectId.type; },
        getSubtype: function(parameter) { return parameter.parameterType; },
        getSubtypeClass: function(subtype) { return ParameterList[subtype]; },
        getParent: function(parameter) { return parameter.parent; },
        children: {},
        getFormKeys: function(parameter) { return [parameter.name]; }
    }
});
