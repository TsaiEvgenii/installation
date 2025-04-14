define([
    '../geometry',
    './_polygon',
    '../../measurements/dimension'
], function(Geom, Polygon, Dimension) {

    let Type = 'vert-triang',
        Name = 'Triangular Vertical Sides',
        Options = {
            height_param: [
                {name: 'Top', value: 'top'},
                {name: 'Bottom', value: 'bottom'}
            ],
            offset_param: [
                {name: 'Left', value: 'left'},
                {name: 'Right', value: 'right'}
            ]
        };

    class VerticalTriangular extends Polygon.Shape {
        constructor() {
            super(Type);
            // height
            this.params.height_param = 'top';
            this.params.height = null;
            this.params.is_height_customizable = false;
            this.params.height_name = 'vert-triangular-height';
            this.params.height_min = 0;
            this.params.height_max = null;
            // offset
            this.params.offset_param = 'left';
            this.params.offset = null;
            this.params.is_offset_customizable = false;
            this.params.offset_name = 'vert-triangular-offset';
            this.params.offset_min = 0;
            this.params.offset_max = null;

            this._measurements = {};
        }

        getMeasurements() {
            return [
                this._getTopHeightMeasurement(),
                this._getBottomHeightMeasurement(),
                this._getOffsetLeftMeasurement(),
                this._getOffsetRightMeasurement()
            ];
        }

        getTopHeight() {
            let heightTotal = this.parent.box.height;
            let height = (this.params.height === null)
                ? heightTotal / 2
                : this.params.height;
            return (this.params.height_param == 'top')
                ? height
                : heightTotal - height;
        }

        setTopHeight(height) {
            if (height === null || this.params.height_param == 'top') {
                this.params.height = height
            } else {
                let heightTotal = this.parent.box.height;
                this.params.height = heightTotal - height;
            }
        }

        getBottomHeight() {
            return this.parent.box.height - this.getTopHeight();
        }

        setBottomHeight(height) {
            if (height === null) {
                this.setTopHeight(null);
            } else {
                this.setTopHeight(this.parent.box.height - height);
            }
        }

        getOffsetLeft() {
            let width = this.parent.box.width;
            let offset = (this.params.offset === null)
                ? width / 2
                : this.params.offset;
            return (this.params.offset_param == 'left')
                ? offset
                : width - offset;
        }

        getOffsetRight() {
            return this.parent.box.width - this.getOffsetLeft();
        }

        setOffsetLeft(offset) {
            if (offset === null || this.params.offset_param == 'left') {
                this.params.offset = offset;
            } else {
                this.params.offset = this.parent.box.width - offset;
            }
        }

        setOffsetRight(offset) {
            if (offset === null) {
                this.setOffsetLeft(null);
            } else {
                this.setOffsetLeft(this.parent.box.width - offset);
            }
        }

        _boxToPathPoints(box) {
            let width = this.parent.box.width,
                height = this.parent.box.height,
                points = [];

            points.push(box.bottomLeft());
            if (this.getOffsetLeft() != 0 && this.getBottomHeight() != 0) {
                points.push(box.bottomLeft().sum(new Geom.Vect(0, -this.getBottomHeight())));
            }
            if (this.getTopHeight() != 0) {
                points.push(box.topLeft().sum(new Geom.Vect(this.getOffsetLeft(), 0)));
            }
            if (this.getOffsetLeft() != width && this.getBottomHeight() != 0) {
                points.push(box.bottomRight().sum(new Geom.Vect(0, -this.getBottomHeight())));
            }
            points.push(box.bottomRight());

            return points;
        }


        // Measurements

        // 1. Top height

        _getTopHeightMeasurement() {
            let startingPoints = this._getTopHeightStartingPoints();
            let measurement = this._measurements['top_height']
                ? this._measurements['top_height']
                : new Dimension(
                    this.getTopHeight.bind(this),
                    this.setTopHeight.bind(this),
                    'right');
            this._measurements['top_height'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable =
                (this.params.is_height_customizable
                 && this.params.height_param == 'top');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.height_name) ? this.params.height_name : 'vert-triangular-height';
                measurement.min = this.params.height_min;
                measurement.max = this.params.height_max;
            }
            measurement.placement = 'right';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        isMeasurementNameValid(string) {
            return string !== null && string.length !== 0;
        }

        _getTopHeightStartingPoints() {
            let box = this.parent.box;
            return [
                box.topRight().sum(new Geom.Vect(0, this.getTopHeight())),
                box.topRight().sum(new Geom.Vect(-this.getOffsetRight()))
            ];
        }

        // 2. Bottom height

        _getBottomHeightMeasurement() {
            let startingPoints = this._getBottomHeightStartingPoints();
            let measurement = this._measurements['bottom_height']
                ? this._measurements['bottom_height']
                : new Dimension(
                    this.getBottomHeight.bind(this),
                    this.setBottomHeight.bind(this));
            this._measurements['bottom_height'] = measurement;
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable =
                (this.params.is_height_customizable
                 && this.params.height_param == 'bottom');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.height_name) ? this.params.height_name : 'vert-triangular-height';
                measurement.min = this.params.height_min;
                measurement.max = this.params.height_max;
            }
            measurement.placement = 'right';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        _getBottomHeightStartingPoints() {
            let box = this.parent.box;
            return [
                box.bottomRight(),
                box.bottomRight().sum(new Geom.Vect(0, -this.getBottomHeight()))
            ];
        }

        // 3. Left offset

        _getOffsetLeftMeasurement() {
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
            measurement.isCustomizable =
                (this.params.is_offset_customizable
                 && this.params.offset_param == 'left');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.offset_name) ? this.params.offset_name : 'vert-triangular-offset';
                measurement.min = this.params.offset_min;
                measurement.max = this.params.offset_max;
            }
            measurement.placement = 'top';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        _getOffsetLeftStartingPoints() {
            let box = this.parent.box;
            return [
                box.topLeft().sum(new Geom.Vect(0, this.getTopHeight())),
                box.topLeft().sum(new Geom.Vect(this.getOffsetLeft(), 0))
            ];
        }

        // 4. Right offset

        _getOffsetRightMeasurement() {
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
            measurement.isCustomizable =
                (this.params.is_offset_customizable
                 && this.params.offset_param == 'right');
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.offset_name) ? this.params.offset_name : 'vert-triangular-offset';
                measurement.min = this.params.offset_min;
                measurement.max = this.params.offset_max;
            }
            measurement.placement = 'top';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            return measurement;
        }

        _getOffsetRightStartingPoints() {
            let box = this.parent.box;
            return [
                box.topRight().sum(new Geom.Vect(-this.getOffsetRight(), 0)),
                box.topRight().sum(new Geom.Vect(0, this.getTopHeight()))
            ];
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
        Shape: VerticalTriangular
    };
})
