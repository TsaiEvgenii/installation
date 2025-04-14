define(function() {
    return [
        {from: 'parameterType', to: 'type', type: 'string'},
        {from: 'name', to: 'name', type: 'string'},
        {from: 'options', to: 'options', type: 'object', list: true}
    ];
})
