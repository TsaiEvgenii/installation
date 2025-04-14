define([
    '../../blocks/block',
    '../additional/parameter',
    '../additional/restriction',
    '../additional/measurement-restriction',
    '../additional/link'
], function(Block, Parameter, Restriction, MeasurementRestriction, Link) {

    function add(block, child) {
        block.add(child);
    }

    function remove(block, child) {
        block.remove(child);
    }

    function insert(block, child) {
        block.insert(child);
    }

    function position(block, child) {
        return block.getPosition(child);
    }

    return {
        name: 'Block',
        getSubtype: function(block) { return null; },
        getSubtypeClass: function(block) { return Block.Block; },
        getParent: function(block) { return block.parent; },
        canBeRoot: true,
        getParams(block) { return Block.Params; },
        children: {
            parameter: {
                subtypes: ['block'],
                list: Parameter.getObjectList,
                add: Parameter.addToObject,
                insert: Parameter.insertIntoObject,
                remove: Parameter.removeFromObject,
                position: Parameter.positionInObject
            },
            restriction: {
                subtypes: ['block'],
                list: Restriction.getObjectList,
                add: Restriction.addToObject,
                insert: Restriction.insertIntoObject,
                remove: Restriction.removeFromObject,
                position: Restriction.positionInObject
            },
            measurement_restriction: {
                subtypes: ['block'],
                list: MeasurementRestriction.getObjectList,
                add: MeasurementRestriction.addToObject,
                insert: MeasurementRestriction.insertIntoObject,
                remove: MeasurementRestriction.removeFromObject,
                position: MeasurementRestriction.positionInObject
            },
            link: {
                subtypes: ['block'],
                list: Link.getObjectList,
                add: Link.addToObject,
                insert: Link.insertIntoObject,
                remove: Link.removeFromObject,
                position: Link.positionInObject
            },
            feature: {
                list: function(block) { return block.features; },
                add: add,
                insert: insert,
                remove: remove,
                position: position
            },
            measurement: {
                list: function(block) { return block.measurements; },
                add: add,
                insert: insert,
                remove: remove,
                position: position
            },
            block: {
                list: function(block) { return block.children; },
                add: add,
                insert: insert,
                remove: remove,
                position: position
            }
        }
    };
})
