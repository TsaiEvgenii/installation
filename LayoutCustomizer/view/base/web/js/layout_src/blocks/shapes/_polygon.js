define([
    '../color',
    '../geometry',
    '../shape'
], function(Color, Geom, Shape) {

    function throwInvalidPlacement(placement) {
        throw "Invalid placement `" + placement + "'";
    }

    function closest(placement, p1, p2) {
        switch (placement) {
        case 'bottom':
            return (p1.y > p2.y) ? p1 : p2;
        case 'left':
            return (p1.x < p2.x) ? p1 : p2;
        case 'top':
            return (p1.y < p2.y) ? p1 : p2;
        case 'right':
            return (p1.x > p2.x) ? p1 : p2;
        }
        throwInvalidPlacement(placement);
    }

    function closestIn(placement, points) {
        return points.reduce(function(result, point) {
            return closest(placement, result, point);
        });
    }

    function startingPoints(placement, points) {
        function getValue(point) {
            switch (placement) {
            case 'bottom':
            case 'top':
                return point.x;
            case 'left':
            case 'right':
                return point.y;
            }
            throwInvalidPlacement(placement);
        }

        let min = [],
            max = [],
            minValue = null,
            maxValue = null;
        points.forEach(function(point) {
            let value = getValue(point)
            // min
            if (minValue === null || value === minValue) {
                minValue = value;
                min.push(point);
            } else if (value < minValue) {
                minValue = value;
                min = [point];
            }
            // max
            if (maxValue === null || value === maxValue) {
                maxValue = value;
                max.push(point);
            } else if (value > maxValue) {
                maxValue = value;
                max = [point];
            }
        });

        // minmax
        let result = [closestIn(placement, min), closestIn(placement, max)];
        if (placement == 'left' || placement == 'right') {
            result = result.reverse();
        }
        return result.map(function(point) { return point.copy(); });
    }

    function offset(placement, points, vertices) {
        let p = closestIn(placement, points),
            v = closestIn(placement, vertices),
            diff = 0;
        switch (placement) {
        case 'bottom': diff = v.y - p.y; break;
        case 'left': diff = p.x - v.x; break;
        case 'top': diff = p.y - v.y; break;
        case 'right': diff = v.x - p.x; break;
        default:
            throwInvalidPlacement(placement);
        }
        return Math.max(0, diff);
    }

    class Polygon extends Shape.Base {
        constructor(type) {
            super(type);
            this._polygon = null;
        }

        place() {
            this._polygon = new Geom.Polygon(this._boxToPathPoints(this.parent.box));
        }

        getDimensionParams(placement) {
            if (!this._polygon) {
                return null; // not placed yet
            }

            let points = startingPoints(placement, this._polygon.vertices());
            return {
                points: points,
                offset: offset(placement, points, this._polygon.vertices())
            };
        }

        canDraw() {
            return this._polygon.valid();
        }

        getBorderLines() {
            return this._getBorderPolygon().edges();
        }

        getPaddingPath() {
            return this._getPaddingPolygon().vertices();
        }

        getInnerBorderLines() {
            return this._getInnerBorderPolygon().edges();
        }

        getInnerPath() {
            return this._getInnerPolygon().vertices();
        }

        getFeatureClipPath() {
            return this._getFeaturePolygon().vertices();
        }

        getFeatureBox() {
            return this._getFeaturePolygon().getBoundingRect();
        }

        _getFeaturePolygon() {
            let block = this.parent,
                innerPolygon = this._getInnerPolygon(),
                innerBox = innerPolygon.getBoundingRect(),
                paddingPolygon = this._getPaddingPolygon(),
                paddingBox = paddingPolygon.getBoundingRect();
            return (block.getFeaturePadding() == block.padding)
                ? ((innerBox.width < paddingBox.width) ? innerPolygon : paddingPolygon)
                : this._polygon.grown(-block.getFeaturePadding());
        }

        _getBorderPolygon() {
            let block = this.parent,
                pad = block.borderPad();
            return this._polygon.grown(block.borderPad());
        }

        _getPaddingPolygon() {
            let block = this.parent;
            return this._getBorderPolygon()
                .grown(-block.border / 2);
        }

        _getInnerBorderPolygon() {
            let block = this.parent,
                adj = -block.padding + block.innerBorderPad();
            return this._polygon.grown(adj)
        }

        _getInnerPolygon() {
            let block = this.parent;
            return this._getInnerBorderPolygon()
                .grown(-block.getInnerBorder() / 2);
        }

        _boxToPathPoints() { return []; }
    }

    return {Shape: Polygon};
});
