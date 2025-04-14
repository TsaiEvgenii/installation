define([
    './block',
    './feature',
    './measurement',
    './parameter',
    './restriction',
    './measurement-restriction',
    './link'
], function(Block, Feature, Measurement, Parameter, Restriction, MeasurementRestriction, Link) {
    return {
        block: Block,
        feature: Feature,
        measurement: Measurement,
        parameter: Parameter,
        restriction: Restriction,
        measurement_restriction: MeasurementRestriction,
        link: Link
    };
});
