define([
    './_dimension'
], function(Dimension) {

    var Type = 'width',
        Name = 'Width',
        Params = {
            adjustment1: {label: 'Adjustment Left', type: 'number'},
            adjustment2: {label: 'Adjustment Right', type: 'number'},
            offset: {label: 'Offset', type: 'number'},
            placement: {
                name: 'Placement',
                type: 'select',
                options: [
                    {name: 'Top', value: 'top'},
                    {name: 'Bottom', value: 'bottom'}
                ]
            },
            min: {label: 'Min', type: 'formula', nullable: true},
            max: {label: 'Max', type: 'formula', nullable: true}
        };

    class Width extends Dimension.Base {
        constructor() {
            super(Type, 'width', ['top', 'bottom']);
            this.params.placement = 'top';
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Measurement: Width
    };
});
