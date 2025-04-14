define([
    './angle',
    './dimension',
    './radius'
], function(Angle, Dimension, Radius) {
    return {
        angle: Angle,
        dimension: Dimension,
        radius: Radius
    };
});
