define([
    '../additional/restrictions/all'
], function(RestrictionList) {

    return {
        name: 'Restriction',
        additional: true,
        getSubtypeByParent: function(object) { return object.objectId.type; },
        getSubtype: function(restriction) { return restriction.restrictionType; },
        getSubtypeClass: function(subtype) { return RestrictionList[subtype]; },
        getParent: function(restriction) { return restriction.parent; },
        children: {}
    }
});
