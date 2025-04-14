define(function() {
    return {
        fields: [
            {name: 'params.bevel', label: 'Bevel Width', type: 'number'},
            {name: 'params.lineWidth', label: 'Line Width', type: 'number', params: {
                isNullable: true, nullText: 'Block Border'
            }},
            {name: 'params.lineColor', label: 'Line Color', type: 'text', params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    };
});
