define(function() {
    return [
        {from: 'option_type_id', to: 'optionId', type: 'integer'},
        {from: 'params', to: 'params', type: 'object', merge: true}
    ];
});
