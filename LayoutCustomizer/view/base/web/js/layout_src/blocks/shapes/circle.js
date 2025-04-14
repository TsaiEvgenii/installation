define([
    '../color',
    '../geometry',
    '../shape',
    '../../measurements/radius',
], function(Color, Geom, Shape, Radius) {

    let Type = 'circle',
        Name = 'Circle';

    class Circle extends Shape.Base {
        constructor() {
            super(Type);
            this.params.radius = null;
            this.params.is_customizable = false;
            this.params.radius_name = null;
            this.params.radius_min = 0;
            this.params.radius_max = null;
            this._center = new Geom.Vect(0, 0);
            this._circle = new Geom.Circle(new Geom.Vect(), 0);
            this._measurements = {};

            this.params.min_dist = 15;
            this.params.reduse_value = 10;
        }

        place() {
            this._circle = new Geom.Circle(this._getCenter(), this.getRadius());
        }

        getMeasurements() {
            return [this._getRadiusMeasurement()];
        }

        getRadius() {
            let box = this.parent.box;
            let radius = (this.params.radius !== null)
                ? this.params.radius
                : Math.min(box.width, box.height) / 2;
            radius = Math.floor(radius);
            let parentWidth = this.getParentDimension(),
                dist = (parentWidth - radius*2) / 2;
            if(dist < this.params.min_dist) {
                while(dist < this.params.min_dist) {
                    dist += this.params.reduse_value/2;
                    radius -= this.params.reduse_value/2;
                }
                while (radius%(this.params.reduse_value/2) !== 0) {
                    radius++;
                }
            }
            return Math.max(0, radius);
        }

        getParentDimension() {
            let parentBLock = this.parent,
                width = 0, adj = 0;
            while (width === 0) {
                if(parentBLock.width) width = parentBLock.width;
                else parentBLock = parentBLock.parent;
            }
            if(parentBLock.getMeasurementByDimension('width')) {
                adj = parentBLock.getMeasurementByDimension('width')._getAdjustmentSum();
            }
            return width + adj;
        }

        setRadius(radius) {
            this.params.radius = radius;
        }

        getBorderCircle() {
            let block = this.parent,
                pad = block.borderPad();
            return this._circle.grown(Math.max(pad, -this._circle.radius));
        }

        getPaddingCircle() {
            let block = this.parent,
                borderCircle = this.getBorderCircle(),
                adj = -block.border / 2;
            return borderCircle.grown(Math.max(adj, -borderCircle.radius));
        }

        getInnerBorderCircle() {
            let block = this.parent,
                pad = -block.padding + block.innerBorderPad();
            return this._circle.grown(Math.max(pad, -this._circle.radius));
        }

        getInnerCircle() {
            let block = this.parent,
                innerBorderCircle = this.getInnerBorderCircle(),
                adj = -block.getInnerBorder() / 2;
            return innerBorderCircle.grown(Math.max(adj, -innerBorderCircle.radius));
        }

        getFeatureBox() {
            return this._getFeatureCircle().getBoundingRect();
        }

        getFeatureClipCircle() {
            return this._getFeatureCircle();
        }

        _getFeatureCircle() {
            let block = this.parent,
                innerCircle = this.getInnerCircle(),
                paddingCircle = this.getPaddingCircle();
            return (block.getFeaturePadding() == block.padding)
                ? ((innerCircle.radius < paddingCircle.radius) ? innerCircle : paddingCircle)
                : this._circle.grown(-block.getFeaturePadding());
        }

        _getCenter() {
            return this.parent.box.center();
        }

        _getRadiusMeasurement() {
            let measurement = this._measurements['radius']
                ? this._measurements['radius']
                : new Radius(
                    this.getRadius.bind(this),
                    this.setRadius.bind(this));
            this._measurements['radius'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = this.params.is_customizable;
            if (measurement.isCustomizable) {
                measurement.name = this.params.radius_name;
                measurement.min = this.params.radius_min;
                measurement.max = this.params.radius_max;
            }
            measurement.center = this._getCenter();
            measurement.radius = this.getRadius();
            return measurement;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Shape: Circle
    };
});
