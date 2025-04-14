define(function() {
    return [
        {from: 'name', to: 'name', type: 'string', nullable: true},
        {from: 'is_customizable', to: 'isCustomizable', type: 'boolean'},
        {from: 'param_id', to: 'objectData.param_id', type: 'string', nullable: true},
        {from: 'params', to: 'params', type: 'params', merge: true}
    ];
});
