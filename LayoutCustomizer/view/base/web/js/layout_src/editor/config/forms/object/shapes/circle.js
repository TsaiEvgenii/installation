define(function() {
    return {
        fields: [
            {name: 'params.radius', label: 'Radius', type: 'number', params: {
                isNullable: true, nullText: 'Block'
            }},
            {name: 'params.is_customizable', label: 'Is Customizable', type: 'checkbox'},
            {name: 'params.radius_name', label: 'Radius Name', type: 'text', params: {
                isNullable: true, nullText: 'None'
            }, depends: [
                {'params.is_customizable': {'=': true}}
            ]},
            {name: 'params.radius_min', label: 'Min. Radiux', type: 'number', params: {
                isNullable: true, nullText: 'None',
            }, depends: [
                {'params.is_customizable': {'=': true}}
            ]},
            {name: 'params.radius_max', label: 'Max. Radiux', type: 'number', params: {
                isNullable: true, nullText: 'None'
            }, depends: [
                {'params.is_customizable': {'=': true}}
            ]}
        ]
    };
});
