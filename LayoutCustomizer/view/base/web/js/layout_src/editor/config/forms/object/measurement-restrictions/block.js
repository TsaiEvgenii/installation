define(function() {
    return {
        fields: [
            {name: 'optionId', label: 'Option', type: 'ext-option-select'},
            {name: 'params.minWidth', label: 'Min. Width', type: 'number', params: {
                isNullable: true, nullText: 'None'
            }},
            {name: 'params.minHeight', label: 'Min. Height', type: 'number', params: {
                isNullable: true, nullText: 'None'
            }},
            {name: 'params.maxWidth', label: 'Max. Width', type: 'number', params: {
                isNullable: true, nullText: 'None'
            }},
            {name: 'params.maxHeight', label: 'Max. Height', type: 'number', params: {
                isNullable: true, nullText: 'None'
            }}
        ]
    }
});
