define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'sliding-door',
        Name = 'Sliding Door',
        Params = {
            type: {label: 'Type', type: 'select', options: [
                    {name: 'Sliding', value: 'sliding'},
                    {name: 'Fixed', value: 'fixed'}
            ]},
        };

    class SlidingDoor extends Feature.Base {
        constructor() {
            super(Type);
            this.params.type = 'none';
        }

        place() {

        }

    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: SlidingDoor
    };
});
