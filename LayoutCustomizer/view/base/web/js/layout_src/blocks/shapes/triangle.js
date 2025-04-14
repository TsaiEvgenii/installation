define([
    '../geometry',
    './_polygon',
    '../../measurements/angle',
    '../../measurements/dimension'
], function(Geom, Polygon, Angle, Dimension) {

    let Type = 'triangle',
        Name = 'Triangle',
        Options = {
            base: [
                {name: 'Bottom', value: 'bottom'},
                {name: 'Left', value: 'left'},
                {name: 'Top', value: 'top'},
                {name: 'Right', value: 'right'}
            ],
            param_type: [
                {name: 'Offset', value: 'offset'},
                {name: 'Angle', value: 'angle'}
            ],
            base_angle: [
                {name: 'Left', value: 'left'},
                {name: 'Right', value: 'right'}
            ],
            offset_from: [
                {name: 'Left', value: 'left'},
                {name: 'Right', value: 'right'}
            ]
        };

    class Triangle extends Polygon.Shape {
        constructor() {
            super(Type);
            this.params.base = 'bottom';
            this.params.param_type = 'offset';
            this.params.show_offsets = true;

            // angle
            this.params.base_angle = 'left';
            this.params.angle_name = 'triangle-angle';
            this.params.angle = null;
            this.params.angle_min = 0;
            this.params.angle_max = null;

            // offset
            this.params.offset_from = 'left';
            this.params.offset = null;
            this.params.offset_name = 'triangle-offset';
            this.params.offset_min = 0;
            this.params.offset_max = null;

            this.params.is_customizable = false;
            this._measurements = {};
        }

        getMeasurements() {
            let measurements = [
                this._getOffsetLeftMeasurement(),
                this._getOffsetRightMeasurement(),
                this._getAngleLeftMeasurement(),
                this._getAngleRightMeasurement()
            ];
            return measurements.filter(item => item !== null);
        }

        getOffsetLeft() {
            let widthTotal = this.parent.box.width;
            if (this.params.param_type == 'offset') {
                // Offset

                if (this.params.offset === null) {
                    return widthTotal / 2;
                } else {
                    return (this.params.offset_from == 'left')
                        ? this.params.offset
                        : widthTotal - this.params.offset;
                }
            } else {
                // Angle

                if (this.params.angle === null || this.params.angle === 0) {
                    return widthTotal / 2;
                } else {
                    let angle = this.params.angle * Math.PI / 180,
                        dist = this.parent.box.height / Math.tan(angle);
                    return (this.params.base_angle == 'right')
                        ? widthTotal - dist
                        : dist;
                }
            }
        }

        setOffsetLeft(offset) {
            if (offset === null || this.params.offset_from == 'left') {
                this.params.offset = offset;
            } else {
                let widthTotal = this.parent.box.width;
                this.params.offset = widthTotal - offset;
            }
        }

        getOffsetRight() {
            return this.parent.box.width - this.getOffsetLeft();
        }

        setOffsetRight(offset) {
            if (offset === null) {
                this.setOffsetLeft(null);
            } else {
                let widthTotal = this.parent.box.width;
                this.setOffsetLeft(widthTotal - offset);
            }
        }

        getAngle() {
            if (this.params.param_type == 'angle') {
                if (this.params.angle === null || this.params.angle === 0) {
                    let offset = this.getOffsetLeft(),
                        height = this.parent.box.height,
                        angle = Math.atan2(height, offset);
                    return angle * 180 / Math.PI;
                } else {
                    return this.params.angle;
                }
            }
            return null;
        }

        setAngle(angle) {
            this.params.angle = angle;
        }

        _boxToPathPoints(box) {
            // Rotate box vertices

            function rotate(values, num) {
                values.push.apply(values, values.splice(0, num));
            }

            let vertices = box.vertices();
            switch (this.params.base) {
            case 'left':
                // rotate(vertices, 0);
                break;
            case 'top':
                rotate(vertices, 1);
                break;
            case 'right':
                rotate(vertices, 2);
                break;
            case 'bottom':
            default:
                rotate(vertices, 3);
                break;
            }

            // Calc. triangle points

            // default: middle point
            function apexDefault(p1, p2) {
                return p1.between(p2);
            }

            function apexOffset(p1, p2, offset) {
                let vect = p2.diff(p1).resized(offset);
                return p1.sum(vect);
            }

            let params = this.params;
            function apex(p1, p2) {
                if (p1.equal(p2)) {
                    return p1;
                }

                switch (params.param_type) {
                case 'angle':
                    if (params.angle !== null && params.angle > 0) {
                        let angle = params.angle * Math.PI / 180,
                            dist = box.height / Math.tan(angle);
                        let offset = (params.base_angle == 'right')
                            ? box.width - dist
                            : dist;
                        return apexOffset(p1, p2, offset);
                    } else {
                        return apexDefault(p1, p2);
                    }
                case 'offset':
                    if (params.offset !== null) {
                        let offset = (params.offset_from == 'right')
                            ? box.width - params.offset
                            : params.offset;
                        return apexOffset(p1, p2, offset);
                    } else {
                        return apexDefault(p1, p2)
                    }
                }
            }

            return [
                vertices[0],
                apex(vertices[1], vertices[2]),
                vertices[3]
            ];
        }


        // Measurements

        _getOffsetMeasurementPlacement() {
            switch (this.params.base) {
            case 'left':
                return 'right';
            case 'top':
                return 'bottom';
            case 'right':
                return 'left';
            case 'bottom':
                return 'top';
            }
        }

        // 1. Offset left

        _getOffsetLeftMeasurement() {
            let isCustomizable =
                (this.params.is_customizable
                 && this.params.param_type == 'offset'
                 && this.params.offset_from == 'left');

            if (!isCustomizable && !this.params.show_offsets) {
                return null;
            }

            let startingPoints = this._getOffsetLeftStartingPoints();
            let measurement = this._measurements['offset_left']
                ? this._measurements['offset_left']
                : new Dimension(
                    this.getOffsetLeft.bind(this),
                    this.setOffsetLeft.bind(this));
            this._measurements['offset_left'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if(measurement.isCustomizable)
                measurement.name = this.isMeasurementNameValid(this.params.offset_name) ? this.params.offset_name : 'triangle-offset';
            measurement.min = this.params.offset_min;
            measurement.max = this.params.offset_max;
            measurement.placement = this._getOffsetMeasurementPlacement();
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        isMeasurementNameValid(string) {
            return string !== null && string.length !== 0;
        }

        _getOffsetLeftStartingPoints() {
            let vertices = this._boxToPathPoints(this.parent.box),
                left = vertices[0],
                apex = vertices[1];
            let placement = this._getOffsetMeasurementPlacement();
            return (placement == 'top' || placement == 'left')
                ? [left, apex]
                : [apex, left];
        }

        // 2. Offset right

        _getOffsetRightMeasurement() {
            let isCustomizable =
                (this.params.is_customizable
                 && this.params.param_type == 'offset'
                 && this.params.offset_from == 'right');

            if (!isCustomizable && !this.params.show_offsets) {
                return null;
            }

            let startingPoints = this._getOffsetRightStartingPoints();
            let measurement = this._measurements['offset_right']
                ? this._measurements['offset_right']
                : new Dimension(
                    this.getOffsetRight.bind(this),
                    this.setOffsetRight.bind(this));
            this._measurements['offset_right'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if(measurement.isCustomizable)
                measurement.name = this.isMeasurementNameValid(this.params.offset_name) ? this.params.offset_name : 'triangle-offset';
            measurement.min = this.params.offset_min;
            measurement.max = this.params.offset_max;
            measurement.placement = this._getOffsetMeasurementPlacement();
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        _getOffsetRightStartingPoints() {
            let vertices = this._boxToPathPoints(this.parent.box),
                apex = vertices[1],
                right = vertices[2];
            let placement = this._getOffsetMeasurementPlacement();
            return (placement == 'top' || placement == 'left')
                ? [apex, right]
                : [right, apex];
        }

        // 3. Angle left

        _getAngleLeftMeasurement() {
            if (this.params.param_type != 'angle' || this.params.base_angle != 'left') {
                return null;
            }

            let vertices = this._boxToPathPoints(this.parent.box),
                left = vertices[0],
                apex = vertices[1],
                right = vertices[2];
            let measurement = this._measurements['angle_left']
                ? this._measurements['angle_left']
                : new Angle(
                    this.getAngle.bind(this),
                    this.setAngle.bind(this));
            this._measurements['angle_left'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable =
                (this.params.is_customizable
                 && this.params.param_type == 'angle'
                 && this.params.base_angle == 'left');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.angle_name) ? this.params.angle_name : 'triangle-angle';
                measurement.min = this.params.angle_min;
                measurement.max = this.params.angle_max;
            }
            measurement.apex = left;
            measurement.point1 = right;
            measurement.point2 = apex;
            return measurement
        }

        // 4. Angle right
        _getAngleRightMeasurement() {
            if (this.params.param_type != 'angle' || this.params.base_angle != 'right') {
                return null;
            }

            let vertices = this._boxToPathPoints(this.parent.box),
                left = vertices[0],
                apex = vertices[1],
                right = vertices[2];
            let measurement = this._measurements['angle_right']
                ? this._measurements['angle_right']
                : new Angle(
                    this.getAngle.bind(this),
                    this.setAngle.bind(this));
            this._measurements['angle_right'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable =
                (this.params.is_customizable
                 && this.params.param_type == 'angle'
                 && this.params.base_angle == 'right');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.angle_name) ? this.params.angle_name : 'triangle-angle';
                measurement.min = this.params.angle_min;
                measurement.max = this.params.angle_max;
            }
            measurement.apex = right;
            measurement.point1 = apex;
            measurement.point2 = left;
            return measurement;
        }

        getMeasurementByDimension(dimension) {
            let substr = dimension === 'width' ? 'offset' : 'height',
                measurementArray = [];
            let measurements = this._measurements;
            Object.keys(measurements).forEach(function(key){
                if(key.includes(substr) && measurements[key].isCustomizable)
                    measurementArray.push(measurements[key]);
            });
            return measurementArray;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Options: Options,
        Shape: Triangle
    };
});
