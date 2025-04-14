define([
    '../../../ui/widgets/dialog/value-tree-select'
], function(ValueTreeSelect) {

    function getObjectTreeValues(context, type) {
        let oh = context.objectHelper,
            om = context.objectManager;
        function fromObjectTree(root) {
            return {
                label: oh.getDefaultName(root),
                value: root.objectId.toString(),
                children: oh.getChildren(root).map(fromObjectTree),
                disabled: (type && type != oh.getType(root))
            };
        }
        return context.rootObjectIds.toArray()
            .map(om.get, om)
            .map(fromObjectTree);
    }

    // Params:
    // - objectType

    class ObjectSelect extends ValueTreeSelect.Widget {
        constructor(context, params, container = null) {
            let values = getObjectTreeValues(context, params.objectType || null);
            super(context, params, values, container);
        }
    };

    return {Widget: ObjectSelect};
});
