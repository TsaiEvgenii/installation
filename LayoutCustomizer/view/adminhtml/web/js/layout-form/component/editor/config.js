define(function() {
    return {
        General: {
            background: 'white'
        },
        "Object": {
            Defaults: {
                _rootBlock: {
                    'pos.x': null,
                    'pos.y': null,
                    padding: 4,
                    spacing: 4
                },
                block: {
                    color: 'f4f5f5',
                    borderColor: '29333c'
                },
                feature: {
                    glass: {
                        "params.color": 'd2e8fe',
                        "params.opacity": 1
                    }
                }
            }
        },
        Data: {
            ImportMapList: {
                block: [
                    {from: 'block_id', to:'objectData._id', type: 'number'}
                ],
                feature: [
                    {from: 'feature_id', to: 'objectData._id', type: 'number'}
                ],
                measurement: [
                    {from: 'measurement_id', to: 'objectData._id', type: 'number'}
                ],
                parameter: [
                    {from: 'parameter_id', to: 'objectData._id', type: 'number'}
                ],
                restriction: [
                    {from: 'restriction_id', to: 'objectData._id', type: 'number'}
                ],
                measurementRestriction: [
                    {from: 'measurement_restriction_id', to: 'objectData._id', type: 'number'}
                ]
            },
            ExportMapList: {
                block: [
                    {from: 'objectData._id', to: 'block_id', type: 'number'}
                ],
                feature: [
                    {from: 'objectData._id', to: 'feature_id', type: 'number'}
                ],
                measurement: [
                    {from: 'objectData._id', to: 'measurement_id', type: 'number'}
                ],
                parameter: [
                    {from: 'objectData._id', to: 'parameter_id', type: 'number'}
                ],
                restriction: [
                    {from: 'objectData._id', to: 'restriction_id', type: 'number'}
                ],
                measurementRestriction: [
                    {from: 'objectData._id', to: 'measurement_restriction_id', type: 'number'}
                ]
            }
        }
    };
});
