define([
    './forms/object/block',
    './forms/object/feature',
    './forms/object/measurement',
    './forms/object/shape',
    './forms/object/parameter',
    './forms/object/restriction',
    './forms/object/measurement-restriction',
    './forms/object/link'
], function(Block, Feature, Measurement, Shape, Parameter, Restriction, MeasurementRestriction, Link) {
    return {
        "Object": {
            block: Block,
            feature: Feature,
            measurement: Measurement,
            shape: Shape,
            parameter: Parameter,
            restriction: Restriction,
            measurement_restriction: MeasurementRestriction,
            link: Link
        }
    };
});
