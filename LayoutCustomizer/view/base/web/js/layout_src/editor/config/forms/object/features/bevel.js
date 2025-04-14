define(function() {
    return {
        fields: [
            {name: 'params.lineColor', label: 'Line Color', type: 'color', params: {
                isNullable: true, nullText: 'Block Border'
            }},
            {name: 'params.lineWidth', label: 'Line Width', type: 'number', params: {
                isNullable: true, nullText: 'Block Border'
            }}
        ]
    };
});
