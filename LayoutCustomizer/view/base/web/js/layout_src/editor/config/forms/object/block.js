define([
    '../../../../blocks/block'
], function(Block) {
    return {
        fields: [
            {name: 'name', label: 'Name', type: 'text', order: 0},
            {name: 'layout', label: 'Layout', type: 'select', order: 10, params: {
                options: [
                    {name: "Horizontal", value: Block.Layout.Horizontal},
                    {name: "Vertical", value: Block.Layout.Vertical}
                ]
            }},

            {name: 'pos.x', label: 'X', type: 'number', order: 20, params: {
                isNullable: true, nullText: 'Auto'
            }},

            {name: 'pos.y', label: 'Y', type: 'number', order: 30, params: {
                isNullable: true, nullText: 'Auto'
            }},

            {name: 'width', label: 'Width', type: 'number', order: 40, params: {
                isNullable: true, nullText: 'Auto'
            }},

            {name: 'height', label: 'Height', type: 'number', order: 50, params: {
                isNullable: true, nullText: 'Auto'
            }},

            {name: 'border', label: 'Border', type: 'number', order: 60},

            {name: 'borderPlacement', label: 'Border Placed', type: 'select', order: 70,
             params: {
                 options: [
                     {name: "Inside", value: Block.BorderPlacement.Inside},
                     {name: "Middle", value: Block.BorderPlacement.Middle},
                     {name: "Outside", value: Block.BorderPlacement.Outside}
                 ]
             },
             depends: [{'border': {'>': 0}}]},

            {name: 'borderColor', label: 'Border Color', type: 'color', order: 80,
             params: {isNullable: true, nullText: 'None'}, depends: [{'border': {'>': 0}}]},

            {name: 'innerBorder', label: 'Inner Border', type: 'number', order: 90, params: {
                isNullable: true, nullText: 'Same'
            }},

            {name: 'innerBorderPlacement', label: 'Inner Border Placed', type: 'select', order: 100,
             params: {
                 options: [
                     {name: "Inside", value: Block.BorderPlacement.Inside},
                     {name: "Middle", value: Block.BorderPlacement.Middle},
                     {name: "Outside", value: Block.BorderPlacement.Outside}
                 ]
             },
             depends: [
                 {'innerBorder': {'not-null': true, '>': 0}},
                 {'innerBorder': {'null': true}, 'border': {'>': 0}}
             ]},

            {name: 'innerBorderColor', label: 'Inner Border Color', type: 'color', order: 110,
             params: {isNullable: true, nullText: 'Same'},
             depends: [
                 {'innerBorder': {'not-null': true, '>': 0}},
                 {'innerBorder': {'null': true}, 'border': {'>': 0}}
             ]},

            {name: 'padding', label: 'Padding', type: 'number', order: 120},

            {name: 'featurePadding', label: 'Feature Padding', type: 'number', order: 130, params: {
                isNullable: true, nullText: 'Same'
            }},

            {name: 'spacing', label: 'Spacing', type: 'number', order: 140},

            {name: 'color', label: 'Color', type: 'color', order: 150, params: {
                isNullable: true, nullText: 'None'
            }},

            {name: 'shape', label: 'Shape', type: 'shape-editor', order: 160},

            {name: 'reverse', label: 'Reverse child', type: 'select', order: 170,
                params: {options: Block.Params.reverse.options},
            }
        ]};
});
