define([
    '../../../../../data/helper',
    './_over_children'
], function(DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.num', label: 'Num', type: 'number', order: 10},
            {name: 'params.angle', label: 'Angle', type: 'number', order: 20},
            {name: 'params.width', label: 'Width', type: 'number', order: 30},
            {name: 'params.centerHeight', label: 'Center Height', type: 'number', order: 40},
            {name: 'params.color', label: 'Color', type: 'color', order: 50, params: {
                isNullable: true, nullText: 'Block Color'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'color', order: 60, params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    });
});
