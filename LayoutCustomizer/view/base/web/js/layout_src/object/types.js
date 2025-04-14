define([
    './types/block',
    './types/feature',
    './types/measurement',
    './types/parameter',
    './types/restriction',
    './types/measurement-restriction',
    './types/link'
], function(Block, Feature, Measurement, Parameter, Restriction, MeasurementRestriction, Link) {

    return {
        block: Block,
        feature: Feature,
        measurement: Measurement,
        parameter: Parameter,
        restriction: Restriction,
        measurement_restriction :MeasurementRestriction,
        link: Link
    };
});
