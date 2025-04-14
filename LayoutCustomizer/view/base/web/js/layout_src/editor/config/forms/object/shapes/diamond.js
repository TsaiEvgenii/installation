define(function() {
    return {
        fields: [
            {name: 'params.width', label: 'Width', type: 'number', params: {
                isNullable: true, nullText: 'Block'
            }},
            {name: 'params.height', label: 'Height', type: 'number', params: {
                isNullable: true, nullText: 'Block'
            }}
        ]
    };
});
