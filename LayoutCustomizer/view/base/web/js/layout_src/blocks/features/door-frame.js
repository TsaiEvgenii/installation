define([
    '../color',
    '../feature',
    '../geometry'
], function(Color, Feature, Geom) {

    var Type = 'door-frame',
        Name = 'Door Frame',
        Params = {
            width: {label: 'Width', type: 'number'},
            color: {label: 'Color', type: 'color'},
            lineColor: {label: 'Line Color', type: 'color'},
            lineWidth: {label: 'Line Width', type: 'number'}
        };

    class DoorFrame extends Feature.Base {
        constructor() {
            super(Type);
            this._bounded = false;
            // default params
            this.params.width = 10;
            this.params.color = null;
            this.params.lineColor = null;
            this.params.lineWidth = null;
            // calculated properties
            this._borderPoints = [];
            this._fillPoints = [];
        }

        place() {
            let outerBox = this.parent.outerBox().copy();
            let width = this.params.width,
                halfLine = this.getLineWidth() / 2;

            this._borderPoints = [
                outerBox.bottomLeft().sum(new Geom.Vect(0, -halfLine)),
                outerBox.bottomLeft().sum(new Geom.Vect(-width, -halfLine)),
                outerBox.topLeft().sum(new Geom.Vect(-width, -width)),
                outerBox.topRight().sum(new Geom.Vect(width, -width)),
                outerBox.bottomRight().sum(new Geom.Vect(width, -halfLine)),
                outerBox.bottomRight().sum(new Geom.Vect(0, -halfLine))
            ];

            {
                this._fillPoints = [];
                let fillThick = (width - halfLine),
                    fillHeight = outerBox.height + width - 3 * halfLine,
                    fillWidth = outerBox.width + 2 * width - 2 * halfLine;
                if (fillThick > 0 && fillHeight > 0 && fillWidth > 0) {
                    let point = outerBox.bottomLeft().sum(new Geom.Vect(0, -(2 * halfLine))),
                        points = [point],
                        moves = [
                            new Geom.Vect(-fillThick, 0),
                            new Geom.Vect(0, -fillHeight),
                            new Geom.Vect(fillWidth, 0),
                            new Geom.Vect(0, fillHeight),
                            new Geom.Vect(-fillThick, 0),
                            new Geom.Vect(0, -(fillHeight - fillThick)),
                            new Geom.Vect(-(fillWidth - 2 * fillThick), 0),
                            new Geom.Vect(0, fillHeight - fillThick)
                        ];
                    moves.forEach(function(move) {
                        point.add(move);
                        points.push(point.copy());
                    });
                    this._fillPoints = points;
                }
            }
        }

        getBoundingRect() {
            let rect = this.parent.outerBox().copy(),
                width = this.params.width,
                halfLine = this.getLineWidth() / 2,
                fullWidth = width + halfLine;
            rect.move(new Geom.Vect(-fullWidth, -fullWidth));
            rect.width += fullWidth * 2;
            rect.height += fullWidth;
            return rect;
        }

        getLineWidth() {
            return (this.params.lineWidth !== null)
                ? this.params.lineWidth
                : this.parent.border
        }

        getBorderPoints() {
            return this._borderPoints;
        }

        getFillPoints() {
            return this._fillPoints;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: DoorFrame
    };
});
