define([
    '../additional/links/all'
], function(LinkList) {

    return {
        name: 'Link',
        additional: true,
        getSubtypeByParent: function(object) { return object.objectId.type; },
        getSubtype: function(link) { return link.linkType; },
        getSubtypeClass: function(subtype) { return LinkList[subtype]; },
        getParent: function(link) { return link.parent; },
        children: {},
        getFormKeys: function(link) { return [link.name]; }
    }
});
