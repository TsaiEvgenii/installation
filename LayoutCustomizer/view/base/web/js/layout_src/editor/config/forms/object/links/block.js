define(function() {
    return {
        fields: [
            {name: 'name', label: 'Field', type: 'label'},
            {name: 'ref', label: 'From', type: 'object-select', params: {objectType: 'block'}}
        ]
    };
});
