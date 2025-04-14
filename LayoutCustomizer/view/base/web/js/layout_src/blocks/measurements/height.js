define([
    './_dimension'
], function(Dimension) {

    var Type = 'height',
        Name = 'Height',
        Params = {
            adjustment1: {label: 'Adjustment Top', type: 'number'},
            adjustment2: {label: 'Adjustment Bottom', type: 'number'},
            offset: {label: 'Offset', type: 'number'},
            placement: {
                name: 'Placement',
                type: 'select',
                options: [
                    {name: 'Left', value: 'left'},
                    {name: 'Right', value: 'right'}
                ]
            },
            min: {label: 'Min', type: 'formula', nullable: true},
            max: {label: 'Max', type: 'formula', nullable: true}
        };

    class Height extends Dimension.Base {
        constructor(params) {
            super(Type, 'height', ['left', 'right']);
            this.params.placement = 'right';
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Measurement: Height
    };
});
