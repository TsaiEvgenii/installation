define([
    '../geometry',
    './_polygon',
    '../../measurements/dimension'
], function(Geom, Polygon, Dimension) {

    let Type = 'trunc-triang',
        Name = 'Truncated Triangular',
        Options = {
            cut_side: [
                {name: 'Left', value: 'left'},
                {name: 'Right', value: 'right'}
            ],
            top_param: [
                {name: 'Top Width', value: 'top_width'},
                {name: 'Top Cut Width', value: 'top_cut_width'}
            ],
            side_param: [
                {name: 'Side Height', value: 'side_height'},
                {name: 'Side Cut Height', value: 'side_cut_height'}
            ]
        };

    class TruncatedTriangular extends Polygon.Shape {
        constructor() {
            super(Type);
            this.params.cut_side = 'left';
            // side
            this.params.side_param = 'side_cut_height';
            this.params.side_height = null;
            this.params.is_side_height_customizable = false;
            this.params.top_width_name = 'trunc-triang-top-width';
            this.params.top_width_min = 0;
            this.params.top_width_max = null;
            this.params.show_top_width = true;
            // top
            this.params.top_param = 'top_cut_width';
            this.params.top_width = null;
            this.params.is_top_width_customizable = false;
            this.params.side_height_name = 'trunc-triang-side-height';
            this.params.side_height_min = 0;
            this.params.side_height_max = null;
            this.params.show_side_height = true;

            this._measurements = {};
        }

        getMeasurements() {
            let measurements = [
                this._getTopWidthMeasurement(),
                this._getTopCutWidthMeasurement(),
                this._getSideHeightMeasurement(),
                this._getSideCutHeightMeasurement()
            ];
            return measurements.filter(item => item !== null);
        }

        getTopWidth() {
            let widthTotal = this.parent.box.width;
            let width = (this.params.top_width === null)
                ? widthTotal / 2
                : this.params.top_width;
            return (this.params.top_param == 'top_width')
                ? width
                : widthTotal - width;
        }

        getTopCutWidth() {
            return this.parent.box.width - this.getTopWidth();
        }

        setTopWidth(width) {
            if (this.params.top_param == 'top_width') {
                this._params.top_width = width;
            } else {
                let widthTotal = this.parent.box.width;
                this._params.top_width = widthTotal - width;
            }
        }

        setTopCutWidth(width) {
            let widthTotal = this.parent.box.width;
            this.setTopWidth(widthTotal - width);
        }

        getSideHeight() {
            let heightTotal = this.parent.box.height;
            let height = (this.params.side_height === null)
                ? heightTotal / 2
                : this.params.side_height;
            return this.params.side_param == 'side_height'
                ? height
                : heightTotal - height;
        }

        getSideCutHeight() {
            return this.parent.box.height - this.getSideHeight();
        }

        setSideHeight(height) {
            if (this.params.side_param == 'side_height') {
                this._params.side_height = height;
            } else {
                let heightTotal = this.parent.box.height;
                this._params.side_height = heightTotal - height;
            }
        }

        setSideCutHeight(height) {
            let heightTotal = this.parent.box.height;
            this.setSideHeight(heightTotal - height);
        }

        _boxToPathPoints(box) {
            // top width
            let topCutWidth = this.getTopCutWidth();
            topCutWidth = Math.min(topCutWidth, box.width);
            topCutWidth = Math.max(topCutWidth, 0);
            // side cut height
            let sideCutHeight = this.getSideCutHeight();
            sideCutHeight = Math.min(sideCutHeight, box.height);
            sideCutHeight = Math.max(sideCutHeight, 0);

            // Calc. points

            if (topCutWidth == 0.0 || sideCutHeight == 0.0) {
                // No cut
                return box.vertices();
            }

            let points = [box.bottomLeft()];
            if (this.params.cut_side == 'right') {
                // Cut on right side
                points.push(box.topLeft());
                if (topCutWidth != box.width) {
                    points.push(box.topRight().sum(new Geom.Vect(-topCutWidth, 0)));
                }
                if (sideCutHeight != box.height) {
                    points.push(box.topRight().sum(new Geom.Vect(0, sideCutHeight)));
                }
            } else {
                // Cut on left side
                if (sideCutHeight != box.height) {
                    points.push(box.topLeft().sum(new Geom.Vect(0, sideCutHeight)));
                }
                if (topCutWidth != box.width) {
                    points.push(box.topLeft().sum(new Geom.Vect(topCutWidth, 0)));
                }
                points.push(box.topRight());
            }
            points.push(box.bottomRight());
            return points;
        }


        // Measurements

        // 1. Top width

        _getTopWidthMeasurement() {
            let isCustomizable =
                (this.params.is_top_width_customizable
                 && this.params.top_param == 'top_width');
            if (!isCustomizable && !this.params.show_top_width) {
                return null;
            }

            let startingPoints = this._getTopWidthStartingPoints();
            let measurement = this._measurements['top_width']
                ? this._measurements['top_width']
                : new Dimension(
                    this.getTopWidth.bind(this),
                    this.setTopWidth.bind(this),
                    'top');
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.top_width_name) ? this.params.top_width_name : 'trunc-triang-top-width';
                measurement.min = this.params.top_width_min;
                measurement.max = this.params.top_width_max;
            }
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            this._measurements['top_width'] = measurement;
            return measurement;
        }

        isMeasurementNameValid(string) {
            return string !== null && string.length !== 0;
        }

        _getTopWidthStartingPoints() {
            let box = this.parent.box,
                cutWidth = this.getTopCutWidth();
            return (this.params.cut_side == 'left')
                ? [box.topLeft().sum(new Geom.Vect(cutWidth, 0)), box.topRight()]
                : [box.topLeft(), box.topRight().sum(new Geom.Vect(-cutWidth, 0))];
        }

        // 2. Top cut width

        _getTopCutWidthMeasurement() {
            let isCustomizable =
                (this.params.is_top_width_customizable
                 && this.params.top_param == 'top_cut_width');
            if (!isCustomizable && !this.params.show_top_width) {
                return null;
            }

            let startingPoints = this._getTopCutWidthStartingPoints();
            let measurement = this._measurements['top_cut_width']
                ? this._measurements['top_cut_width']
                : new Dimension(
                    this.getTopCutWidth.bind(this),
                    this.setTopCutWidth.bind(this),
                    'top');
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.top_width_name) ? this.params.top_width_name : 'trunc-triang-top-width';
                measurement.min = this.params.top_width_min;
                measurement.max = this.params.top_width_max;
            }
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            this._measurements['top_cut_width'] = measurement;
            return measurement;
        }

        _getTopCutWidthStartingPoints() {
            let box = this.parent.box,
                cutWidth = this.getTopCutWidth(),
                cutHeight = this.getSideCutHeight();
            return (this.params.cut_side == 'left')
                ? [
                    box.topLeft().sum(new Geom.Vect(0, cutHeight)),
                    box.topLeft().sum(new Geom.Vect(cutWidth, 0))
                ]
                : [
                    box.topRight().sum(new Geom.Vect(-cutWidth, 0)),
                    box.topRight().sum(new Geom.Vect(0, cutHeight))
                ];
        }

        // 3. Side height

        _getSideHeightMeasurement() {
            let isCustomizable =
                (this.params.is_side_height_customizable
                 && this.params.side_param == 'side_height');
            if (!isCustomizable && !this.params.show_side_height) {
                return null;
            }

            let startingPoints = this._getSideHeightStartingPoints();
            let measurement = this._measurements['side_height']
                ? this._measurements['side_height']
                : new Dimension(
                    this.getSideHeight.bind(this),
                    this.setSideHeight.bind(this),
                    'left');
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.side_height_name) ? this.params.side_height_name : 'trunc-triang-side-height';
                measurement.min = this.params.side_height_min;
                measurement.max = this.params.side_height_max;
            }
            measurement.placement = (this.params.cut_side == 'left') ? 'left' : 'right';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            this._measurements['side_height'] = measurement;
            return measurement;
        }

        _getSideHeightStartingPoints() {
            let box = this.parent.box,
                cutHeight = this.getSideCutHeight();
            return (this.params.cut_side == 'left')
                ? [box.bottomLeft(), box.topLeft().sum(new Geom.Vect(0, cutHeight))]
                : [box.bottomRight(), box.topRight().sum(new Geom.Vect(0, cutHeight))];

        }

        // 4. Side cut height

        _getSideCutHeightMeasurement() {
            let isCustomizable =
                (this.params.is_side_height_customizable
                 && this.params.side_param == 'side_cut_height');
            if (!isCustomizable && !this.params.show_side_height) {
                return null;
            }

            let startingPoints = this._getSideCutHeightStartingPoints();
            let measurement = this._measurements['side_cut_height']
                ? this._measurements['side_cut_height']
                : new Dimension(
                    this.getSideCutHeight.bind(this),
                    this.setSideCutHeight.bind(this),
                    'left');
            measurement.parent = this;
            measurement.hilightObjectId = this.parent.objectId.copy();
            measurement.data.objectId = this.parent.objectId.copy();
            measurement.isCustomizable = isCustomizable;
            if (measurement.isCustomizable) {
                measurement.name = this.isMeasurementNameValid(this.params.side_height_name) ? this.params.side_height_name : 'trunc-triang-side-height';
                measurement.min = this.params.side_height_min;
                measurement.max = this.params.side_height_max;
            }
            measurement.placement = (this.params.cut_side == 'left') ? 'left' : 'right';
            measurement.point1 = startingPoints[0];
            measurement.point2 = startingPoints[1];
            this._measurements['side_cut_height'] = measurement;
            return measurement;
        }

        _getSideCutHeightStartingPoints() {
            let box = this.parent.box,
                cutWidth = this.getTopCutWidth(),
                cutHeight = this.getSideCutHeight();
            return (this.params.cut_side == 'left')
                ? [
                    box.topLeft().sum(new Geom.Vect(0, cutHeight)),
                    box.topLeft().sum(new Geom.Vect(cutWidth, 0))
                ]
                : [
                    box.topRight().sum(new Geom.Vect(0, cutHeight)),
                    box.topRight().sum(new Geom.Vect(-cutWidth, 0))
                ];
        }

        getMeasurementByDimension(dimension) {
            let substr = dimension === 'width' ? 'width' : 'height',
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
        Shape: TruncatedTriangular
    };
});
