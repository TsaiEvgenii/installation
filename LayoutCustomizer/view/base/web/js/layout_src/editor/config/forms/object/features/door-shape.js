define([
    '../../../../../blocks/features/door-shape',
    '../../../../../data/helper',
    './_over_children'
], function(DoorShape, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.type', label: 'Type', type: 'select', params: {
                options: DoorShape.Params.type.options
            }},
            {
                name: 'params.side', label: 'Side', type: 'select',
                params: {options: DoorShape.Params.side.options}
            },
            {
                name: 'params.squaresNumber', label: 'Number of squares', type: 'number',
                depends: [
                    {'params.type': {'=': 'squares'}}
                ]
            },
            {
                name: 'params.squareWidth', label: 'Squares Width', type: 'number',
                params: {
                    isNullable: true, nullText: 'Auto'
                },
                depends: [
                    {'params.type': {'=': 'squares'}}
                ]
            },
            {
                name: 'params.squareHeight', label: 'Squares Height', type: 'select',
                params: {options: DoorShape.Params.squareHeight.options},
                depends: [
                    {'params.type': {'=': 'squares'}}
                ]
            },
            {
                name: 'params.squareDistance', label: 'Distance between squares', type: 'number',
                depends: [
                    {'params.type': {'=': 'squares'}}
                ]
            },
            {
                name: 'params.squaresSide', label: 'Squares Side', type: 'select',
                params: {options: DoorShape.Params.squaresSide.options},
                depends: [
                    {'params.type': {'=': 'squares'}}
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
