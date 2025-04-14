define([
    '../../../../../blocks/features/clamp',
    '../../../../../data/helper',
    './_over_children'
], function(Clamp, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.type', label: 'Type', type: 'select', params: {
                options: Clamp.Params.type.options
            }},
            {
                name: 'params.side', label: 'Side', type: 'select',
                params: {options: Clamp.Params.side.options}
            },
            {name: 'params.height', label: 'Height', type: 'number'},
            {name: 'params.lineWidth', label: 'Line Width', type: 'number', params: {
                isNullable: true, nullText: 'Block Border'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'color', params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    });
});
