define([
    '../../../ui/widgets/dialog/value-tree-select',
    '../../helper/ext-values'
], function(ValueTreeSelect, ExtValueHelper) {

    class ExtValueSelect extends ValueTreeSelect.Widget {
        constructor(context, params, extValues, container = null) {
            let values = ExtValueHelper.toValueTree(extValues);
            super(context, params, values, container);
        }
    }

    return {Widget: ExtValueSelect};
});
