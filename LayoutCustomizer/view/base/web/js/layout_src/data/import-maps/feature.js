define(function() {
    return [
        {from: 'show_over_children', to: 'showOverChildren', type: 'boolean'},
        {from: 'params', to: 'params', type: 'params', merge: true},
        {from: 'parameters', add: true, type: 'Object', list: true}
    ];
});
