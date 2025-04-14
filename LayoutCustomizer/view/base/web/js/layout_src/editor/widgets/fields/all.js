define([
    '../../../data/helper',
    '../../../ui/widgets/fields/all',
    './ext-option-list',
    './ext-option-select',
    './ext-param-select',
    './formula',
    './object-select',
    './shape-editor'
], function(
    DataHelper,
    DefaultFieldList,
    ExtOptionList,
    ExtOptionSelect,
    ExtParamSelect,
    Formula,
    ObjectSelect,
    ShapeEditor) {

    return DataHelper.merged(DefaultFieldList, {
        "ext-option-list": ExtOptionList.Widget,
        "ext-option-select": ExtOptionSelect.Widget,
        "ext-param-select": ExtParamSelect.Widget,
        "formula": Formula.Widget,
        "object-select": ObjectSelect.Widget,
        "shape-editor": ShapeEditor.Widget
    });
});
