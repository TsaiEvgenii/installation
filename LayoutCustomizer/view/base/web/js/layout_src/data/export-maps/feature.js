define(function() {
    return [
        {from: 'featureType', to: 'type', type: 'string'},
        {from: 'showOverChildren', to: 'show_over_children', type: 'boolean'},
        {from: 'params', to: 'params', type: 'params'},
        {from: 'objectData.parameters', to: 'parameters', type: 'Object', list: true}
    ];
});
