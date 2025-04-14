define(function() {
    return [
        {from: 'measurementType', to: 'type', type: 'string'},
        {from: 'name', to: 'name', type: 'string', nullable: true},
        {from: 'isCustomizable', to: 'is_customizable', type: 'boolean'},
        {from: 'objectData.param_id', to: 'param_id', type: 'number', nullable: true},
        {from: 'params', to: 'params', type: 'params'}
    ];
});
