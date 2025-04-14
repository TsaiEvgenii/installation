define([
    './base',
    '../blocks/geometry'
], function(Base, Geom) {

    class Angle extends Base {
        constructor(getter, setter, apex, point1, point2) {
            super('angle', getter, setter);
            this._apex = apex ? apex.copy() : null;
            this._point1 = point1 ? point1.copy() : null;
            this._point2 = point2 ? point2.copy() : null;
            this._radius = 15;
        }

        getClipPoints() {
            let radius = this.radius,
                apex = this.apex,
                point1 = this.point1,
                point2 = this.point2;
            // Adjust points so circle with given radius can fit
            // TODO: doesn't work for angles > 90
            let lineWidth = 1; // TODO
            let point1Adj = apex.sum(point1.diff(apex).resized(radius + lineWidth / 2)),
                point2Adj = apex.sum(point2.diff(apex).resized(radius + lineWidth / 2)),
                point3Adj = point2Adj.sum(point1Adj.diff(apex));
            return [apex, point2Adj, point3Adj, point1Adj];
        }

        getCircle() {
            return new Geom.Circle(this._apex, this._radius)
        }

        getTextLine() {
            let radius = this._radius,
                apex = this._apex,
                point1 = this._point1,
                point2 = this._point2;
            let linePoint1 = apex.sum(
                point1.diff(apex).resized(1).sum(point2.diff(apex).resized(1)).resized(radius * 2));
            let linePoint2 = linePoint1.sum(new Geom.Vect(1, 0));
            return new Geom.Segment(linePoint1, linePoint2);
        }

        get apex() { return this._apex; }
        set apex(apex) { this._apex = apex; }

        get point1() { return this._point1; }
        set point1(point1) { this._point1 = point1; }

        get point2() { return this._point2; }
        set point2(point2) { this._point2 = point2; }

        get radius() { return this._radius; }
    }

    return Angle;
});
