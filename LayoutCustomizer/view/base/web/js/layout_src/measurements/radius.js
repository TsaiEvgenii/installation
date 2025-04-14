define([
    './base',
    '../blocks/geometry'
], function(Base, Geom) {

    class Radius extends Base {
        constructor(getter, setter, center, radius) {
            super('radius', getter, setter);
            this._center = center ? center.copy() : null;
            this._radius = radius ? radius : null;
        }

        getLine() {
            return new Geom.Segment(
                this._center,
                this._center.sum((new Geom.Vect(1, -1).resized(this._radius))));
        }

        get center() { return this._center; }
        set center(center) { this._center = center; }

        get radius() { return this._radius; }
        set radius(radius) { this._radius = radius; }
    }

    return Radius;
});
