define([
    '../feature'
], function(Feature) {

    let Type = 'measurement-dependency',
        Name = 'Measurement Dependency',
        Params = {
            breakPoint: { label: "Break Point", type: "number"}
        };

    class MeasurementDependency extends Feature.Base {
        constructor() {
            super(Type);
            this.params.breakPoint = 0;
        }

        get breakPoint() { return this.params.breakPoint; }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: MeasurementDependency
    };
});
