define([
    './checkbox',
    './color',
    './label',
    './number',
    './option-list',
    './select',
    './text'
], function(
    Checkbox,
    Color,
    Label,
    Number,
    OptionList,
    Select,
    Text) {
    return {
        checkbox: Checkbox.Widget,
        color: Color.Widget,
        label: Label.Widget,
        number: Number.Widget,
        "option-list": OptionList.Widget,
        select: Select.Widget,
        text: Text.Widget
    };
});
