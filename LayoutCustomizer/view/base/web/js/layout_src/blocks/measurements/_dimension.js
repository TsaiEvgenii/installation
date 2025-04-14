define([
    '../block',
    '../geometry',
    '../measurement',
    '../../measurements/dimension'
], function(Block, Geom, Measurement, Dimension) {

    class DimensionBase extends Measurement.Base {
        constructor(type, field, validPlacements) {
            super(type);
            this._field = field;
            this.params.adjustment1 = 0;
            this.params.adjustment2 = 0;
            this.params.offset = 0;
            this.params.min = 0;
            this.params.max = null;
            this._validPlacements = validPlacements;
            this._measurement = null;
        }

        place() {
            this.checkPlacement(this.params.placement);
        }

        getMeasurement() {
            let params = this._getStartingPointsAndOffset(),
                startingPoints = params.points,
                offset = params.offset;

            let measurement = this._measurement
                ? this._measurement
                : new Dimension(
                    this.getValue.bind(this),
                    this.setValue.bind(this));
            this._measurement = measurement;
            measurement.parent = this;
            measurement.name = this.name;
            measurement.hilightObjectId = this.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = this.isCustomizable;
            measurement.min = this.params.min;
            measurement.max = this.params.max;
            measurement.placement = this.params.placement;
            measurement.point1 = this._outward(startingPoints[0], this.params.offset);
            measurement.point2 = this._outward(startingPoints[1], this.params.offset);
            measurement.offset = offset;
            return measurement;
        }

        checkPlacement(placement) {
            if (this._validPlacements.indexOf(placement) == -1) {
                throw "Invalid placement `" + placement
                    + "' for `" + this.measurementType + "' measurement";
            }
        }

        getField() {
            return this._field;
        }

        setCheckMeasurementValue(value) {
            let newValue = value ? value : this._measurement.getValue();
            this._measurement._input.setValueWithCheck(newValue);
        }

        getBlockValue() {
            let block = this.parent;
            return block[this._field];
        }

        getValue() {
            let block = this.parent;
            return block.box[this._field] + this._getAdjustmentSum();
        }

        setValue(value) {
            let block = this.parent;
            block[this._field] = (value !== null && value.toString().trim() != 0)
                ? value - this._getAdjustmentSum()
                : block[this._field];
        }

        setMaxParam(value) {
            this.params.max = value;
        }

        _getStartingPointsAndOffset() {
            let block = this.parent;
            let shapeParams = block.isRoot()
                ? block.shape.getDimensionParams(this.params.placement)
                : null;
            shapeParams = (shapeParams || {
                points: this._getStartingPointsDefault(),
                offset: 0
            });
            this._applyStartingPointAdjustments(...shapeParams.points);
            return shapeParams;
        }

        _applyStartingPointAdjustments(p1, p2) {
            switch (this.params.placement) {
            case 'left':
                p1.y += this.params.adjustment2;
                p2.y -= this.params.adjustment1;
                break;
            case 'right':
                p1.y += this.params.adjustment2;
                p2.y -= this.params.adjustment1;
                break;
            case 'top':
                p1.x -= this.params.adjustment1;
                p2.x += this.params.adjustment2;
                break;
            case 'bottom':
                p1.x -= this.params.adjustment1;
                p2.x += this.params.adjustment2;
                break;
            }
        }

        _getStartingPointsDefault() {
            let box = this.parent.box,
                outerBox = this.parent.root().outerBox();
            // Extension line starting points
            var p1 = new Geom.Vect(),
                p2 = new Geom.Vect();
            switch (this.params.placement) {
            case 'left':
                p1.x = outerBox.pos.x;
                p1.y = box.pos.y + box.height;
                p2.x = outerBox.pos.x;
                p2.y = box.pos.y;
                break;
            case 'right':
                p1.x = outerBox.pos.x + outerBox.width;
                p1.y = box.pos.y + box.height;
                p2.x = outerBox.pos.x + outerBox.width;
                p2.y = box.pos.y;
                break;
            case 'top':
                p1.x = box.pos.x;
                p1.y = outerBox.pos.y;
                p2.x = box.pos.x + box.width;
                p2.y = outerBox.pos.y;
                break;
            case 'bottom':
                p1.x = box.pos.x;
                p1.y = outerBox.pos.y + outerBox.height;
                p2.x = box.pos.x + box.width;
                p2.y = outerBox.pos.y + outerBox.height;
                break;
            }
            return [p1, p2];
        }

        _outward(point, distance) {
            let result = point.copy(),
                placement = this.params.placement;
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

        _getAdjustmentSum() {
            return this.params.adjustment1 + this.params.adjustment2;
        }
    }

    return {Base: DimensionBase};
});
