define(function() {
    return {
        fields: [
            {name: 'params.width', label: 'Width', type: 'number'},
            {name: 'params.color', label: 'Color', type: 'color', params: {
                isNullable: true, nullText: 'Block Color'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'color', params: {
                isNullable: true, nullText: 'Block Border'
            }},
            {name: 'params.lineWidth', label: 'Line', type: 'number', params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    };
});
