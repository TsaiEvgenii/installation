define([
    './base',
    '../blocks/geometry'
], function(Base, Geom) {

    class Dimension extends Base {
        constructor(getter, setter, placement, point1, point2) {
            super('dimension', getter, setter);
            this._placement = placement ? placement : null;
            this._point1 = point1 ? point1.copy() : null;
            this._point2 = point2 ? point2.copy() : null;
            this._extension = 3;
            this._distance = 0;
            this._offset = 0;
        }

/*
  $this._placement == 'right'

                  this._extension
                   v
   p1 *-----------+-
                  |
                  |
        p2 *------+-
      |____|______|
        ^      ^ this._distance : distance from "outward"-most point to connection line
 extensions[1]
 in this case extensions[0] is 0, 
 this values allow for lenght difference between extension lines

this._offset value allows to manually move starting points (p1, p2) "outward"
*/

        getExtensionLines() {
            let extensions = this._getExtensions(),
                line1 = new Geom.Segment(
                    this._point1,
                    this._outward(
                        this._point1,
                        this._offset + this._distance + extensions[0] + this._extension)),
                line2 = new Geom.Segment(
                    this._point2,
                    this._outward(
                        this._point2,
                        this._offset + this._distance + extensions[1] + this._extension));
            return [line1, line2];
        }

        getConnectionLine() {
            let extensions = this._getExtensions();
            return new Geom.Segment(
                this._outward(this._point1, this._offset + this._distance + extensions[0]),
                this._outward(this._point2, this._offset + this._distance + extensions[1]));
        }

        getBoundingRect() {
            let lines = this.getExtensionLines();
            return lines[0].getBoundingRect()
                .combined(lines[1].getBoundingRect());
        }

/*
For height measurements:
(d is additional extension line lenght)

                 | placement == 'left' | placement == 'right' |
-----------------+---------------------+----------------------|
                 |  -+-------*p1       |        p1*---+-      |
p1.x - p2.x >= 0 |   |                 |              |       |
 (p1.x >= p2.x)  |  -+---*p2           |    p2*-------+-      |
                 |              [d, 0] |               [0, d] |
-----------------+---------------------+----------------------|
                 |  -+---*p1           |    p1*-------+-      |
p1.x - p2.x < 0  |   |                 |              |       |
 (p1.x < p2.x)   |  -+-------*p2       |        p2*---+-      |
                 |              [0, d] |               [d, 0] |
-----------------+---------------------+----------------------+
*/

        _getExtensions() {
            let extension1 = 0,
                extension2 = 0,
                placement = this._placement;
            if (placement == 'left' || placement == 'right') {
                let diff = this.point1.x - this.point2.x,
                    diffAbs = Math.abs(diff);
                return (placement == 'left') == (diff > 0)
                    ? [diffAbs, 0]
                    : [0, diffAbs];
            } else if (placement == 'top' || placement == 'bottom') {
                let diff = this.point1.y - this.point2.y,
                    diffAbs = Math.abs(diff);
                return (placement == 'top') == (diff > 0)
                    ? [diffAbs, 0]
                    : [0, diffAbs];
            } else {
                throw "Invalid placement `" + placement + "'";
            }
        }

        _outward(point, distance) {
            let result = point.copy(),
                placement = this._placement;
            switch (placement) {
            case 'left':
                result.x -= distance;
                break;
            case 'right':
                result.x += distance;
                break;
            case 'top':
                result.y -= distance;
                break;
            case 'bottom':
                result.y += distance;
                break;
            default:
                throw "Invalid placement value";
            }
            return result;
        }

        get point1() { return this._point1; }
        set point1(point1) { this._point1 = point1; }

        get point2() { return this._point2; }
        set point2(point2) { this._point2 = point2; }

        get placement() { return this._placement; }
        set placement(placement) { this._placement = placement; }

        get distance() { return this._distance; }
        set distance(distance) { this._distance = distance; }

        get offset() { return this._offset; }
        set offset(offset) { this._offset = offset; }
    }

    return Dimension;
});
