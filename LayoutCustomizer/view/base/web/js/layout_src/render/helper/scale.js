define([
    '../../blocks/geometry'
], function(Geom) {

    function scaleVect(offset, scale, vect) {
        return offset.sum(vect.product(scale));
    }

    function scaleSegment(offset, scale, segment) {
        return new Geom.Segment(
            scaleVect(offset, scale, segment.p1),
            scaleVect(offset, scale, segment.p2));
    }

    return {
        scaleVect: scaleVect,
        scaleSegment: scaleSegment
    };
});
