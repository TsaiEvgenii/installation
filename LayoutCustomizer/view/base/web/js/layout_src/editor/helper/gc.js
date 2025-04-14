define(function() {

    function cleanup(context) {
        let om = context.objectManager,
            history = context.commandHistory,
            unusedIds = om.getRemovedObjectIds().diff(history.getObjectIds());
        unusedIds.toArray().forEach(om.destroy, om);
    }

    return {cleanup: cleanup};
});
