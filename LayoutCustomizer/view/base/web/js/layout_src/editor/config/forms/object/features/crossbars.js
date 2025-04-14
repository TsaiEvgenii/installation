define([
    '../../../../../blocks/features/crossbars',
    '../../../../../data/helper',
    './_over_children'
], function(Crossbars, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.numHorizontal', label: 'X', type: 'number', order: 10},
            {name: 'params.numVertical', label: 'Y', type: 'number', order: 20},
            {name: 'params.width', label: 'Width', type: 'number', order: 30},
            {name: 'params.placement', label: 'Placement', type: 'select', order: 40,
                params: {
                    options: Crossbars.Params.placement.options
                },
                depends: [
                    {'params.numHorizontal': {'=': 0}},
                    {'params.numVertical': {'=': 1}}
                ]
            },
            {name: 'params.color', label: 'Color', type: 'color', order: 50, params: {
                isNullable: true, nullText: 'Block Color'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'color', order: 60, params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    });
});
