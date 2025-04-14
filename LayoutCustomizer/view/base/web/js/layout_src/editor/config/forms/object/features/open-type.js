define([
    '../../../../../blocks/features/open-type',
    '../../../../../data/helper',
    './_over_children'
], function(OpenType, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.type', label: 'Type', type: 'select', params: {
                options: OpenType.Params.type.options
            }},
            {
                name: 'params.side', label: 'Side', type: 'select',
                params: {options: OpenType.Params.side.options},
                depends: [
                    {'params.type': {'=': 'hinged-left'}},
                    {'params.type': {'=': 'hinged-right'}},
                    {'params.type': {'=': 'slide-left'}},
                    {'params.type': {'=': 'slide-right'}},
                ]
            },
            {name: 'params.lineWidth', label: 'Line Width', type: 'number', params: {
                isNullable: true, nullText: 'Block Border'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'color', params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    });
});
