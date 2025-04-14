define([
    '../color',
    '../feature',
    '../geometry'
], function(Color, Feature, Geom) {

    var Type = 'open-type',
        Name = 'Opening Type',
        Params = {
            type: {label: 'Type', type: 'select', options: [
                {name: 'None', value: 'none'},
                {name: 'Hinged Left', value: 'hinged-left'},
                {name: 'Hinged Right', value: 'hinged-right'},
                {name: 'Top-guided', value: 'top-guided'},
                {name: 'Bottom-guided', value: 'bottom-guided'},
                {name: 'Turn/tilt Left', value: 'turn-tilt-left'},
                {name: 'Turn/tilt Right', value: 'turn-tilt-right'},
                {name: 'Slide Left', value: 'slide-left'},
                {name: 'Slide Right', value: 'slide-right'},
                {name: 'Top-swing', value: 'top-swing'},
                {name: 'Top-guided-w-fire-escape', value: 'top-guided-w-fire-escape'},
                {name: 'Side-slide', value: 'side-slide'},
                {name: 'Tilt/Slide left', value: 'tilt-slide-left'},
                {name: 'Tilt/Slide right', value: 'tilt-slide-right'},
                {name: 'Side-guided left', value: 'side-guided-left'},
                {name: 'Side-guided right', value: 'side-guided-right'},
            ]},
            side: {label: 'Side (hinged)', type: 'select', options: [
                {name: 'Out', value: 'out'},
                {name: 'In', value: 'in'}
            ]},
            lineColor: {label: 'Line Color', type: 'color'},
            borderColor: {label: 'Border Color', type: 'color'}
        };

    class OpenType extends Feature.Base {
        constructor() {
            super(Type);
            this.params.type = 'hinged-left';
            this.params.side = 'in';
            this.params.lineColor = null;
            this.params.lineWidth = null;
            this._lines = [];
            this._imageBox = null;
        }

        place() {
            this._lines = this._getLines();
        }

        getLines() {
            return this._lines;
        }

        getLineWidth() {
            return this.params.lineWidth !== null
                ? this.params.lineWidth
                : this.parent.border;
        }

        getLineColor() {
            return this.params.lineColor !== null
                ? this.params.lineColor
                : this.parent.borderColor;
        }

        getLineDash() {
            let params = this.params;
            if ((params.type == 'hinged-left' && params.side == 'in')
                || (params.type == 'hinged-right' && params.side == 'in')
                || params.type == 'turn-tilt-left'
                || params.type == 'turn-tilt-right'
                || params.type == 'bottom-guided'
                || params.type == 'tilt-slide-left'
                || params.type == 'tilt-slide-right'
                || ((params.type == 'slide-left' || params.type == 'slide-right') && params.side == 'in'))
            {
                return [12, 10 + (this.getLineWidth() || 0)];
            } else {
                return [];
            }
        }

        _getLines() {
            this._arrow = null;
            switch (this.params.type) {
            case 'none':
                // return this._getLinesNone(); // "F" symbol
                return [];
            case 'hinged-left':
                return this._getLinesHinged('left');
            case 'hinged-right':
                return this._getLinesHinged('right');
            case 'top-guided':
                return this._getLinesTopGuided();
            case 'bottom-guided':
                return this._getLinesBottomGuided();

            case 'side-guided-left':
                return this._getLinesSideGuided('left');
            case 'side-guided-right':
                return this._getLinesSideGuided('right');

            case 'turn-tilt-left':
                return this._getLinesHinged('right').concat(this._getLinesHinged('bottom'));
            case 'turn-tilt-right':
                return this._getLinesHinged('left').concat(this._getLinesHinged('bottom'));
            case 'slide-left':
                return this._getLinesSlide('left');
            case 'slide-right':
                return this._getLinesSlide('right');
            case 'top-swing':
                return this._getLinesTopSwing();
            case 'top-guided-w-fire-escape':
                return this._getLinesFireEscape();
            case 'side-slide':
                return this._getLinesSideSlide();
            case 'tilt-slide-left': {
                this._arrow = this._getLinesTiltSlide('left');
                return this._getLinesBottomGuided();
            }
            case 'tilt-slide-right': {
                this._arrow = this._getLinesTiltSlide('right');
                return this._getLinesBottomGuided();
            }
            default:
                throw "Invalid type: `" + this.params.type + "'";
            }
        }

        _getBox() {
            let halfLineWidth = this.getLineWidth() / 2;
            return this.parent.shape.getFeatureBox()
                .grown(new Geom.Vect(-halfLineWidth, -halfLineWidth));
        }

        _getLinesHinged(direction) {
            let rect = this._getBox()
            switch (direction) {
            case 'right': {
                return Geom.pointsToSegments([
                    rect.topRight(),
                    rect.topLeft().between(rect.bottomLeft()),
                    rect.bottomRight()
                ]);
            }
            case 'left':
                return Geom.pointsToSegments([
                    rect.topLeft(),
                    rect.topRight().between(rect.bottomRight()),
                    rect.bottomLeft()
                ]);
            case 'bottom':
                return Geom.pointsToSegments([
                    rect.bottomLeft(),
                    rect.topRight().between(rect.topLeft()),
                    rect.bottomRight()
                ]);
            case 'top':
                return Geom.pointsToSegments([
                    rect.topLeft(),
                    rect.bottomLeft().between(rect.bottomRight()),
                    rect.topRight()
                ]);
            default:
                throw "Invalid direction `" + direction + "' for hinged type";
            }
            this._imageBox = null;
        }

        _getLinesSlide(direction) {
            let rect = this._getBox();
            let padTop = rect.height * 0.15,
                padBottom = rect.height * 0.05,
                padSide = rect.width * 0.2,
                padArrow = rect.width * 0.25,
                arrowHeight = rect.width * 0.15,
                arrowLength = arrowHeight / 2;

            switch (direction) {
            case 'left': {
                let arrowPoint = rect.topRight().sum(new Geom.Vect(-padArrow, padTop)),
                    points = [
                        rect.bottomLeft().sum(new Geom.Vect(padSide, -padBottom)),
                        rect.topLeft().sum(new Geom.Vect(padSide, padTop)),
                        arrowPoint
                    ],
                    arrowPoints = [
                        arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2)),
                        arrowPoint.sum(new Geom.Vect(0, -arrowHeight / 2)),
                        arrowPoint.sum(new Geom.Vect(arrowLength, 0)),
                        arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2))
                    ];
                return Geom.pointsToSegments(points).concat(Geom.pointsToSegments(arrowPoints));
            }
            case 'right': {
                let arrowPoint = rect.topLeft().sum(new Geom.Vect(padArrow, padTop)),
                    points = [
                        rect.bottomRight().sum(new Geom.Vect(-padSide, -padBottom)),
                        rect.topRight().sum(new Geom.Vect(-padSide, padTop)),
                        arrowPoint
                    ],
                    arrowPoints = [
                        arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2)),
                        arrowPoint.sum(new Geom.Vect(0, -arrowHeight / 2)),
                        arrowPoint.sum(new Geom.Vect(-arrowLength, 0)),
                        arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2))
                    ];
                return Geom.pointsToSegments(points).concat(Geom.pointsToSegments(arrowPoints));
            }
            default:
                throw "Invalid direction `" + direction + "' for slide type";
            }
            this._imageBox = null;
        }

        _getLinesTopGuided() {
            let rect = this._getBox(),
                pad = rect.height * 0.15,
                points = [
                    rect.topRight().sum(new Geom.Vect(0, pad)),
                    rect.bottom().center(),
                    rect.topLeft().sum(new Geom.Vect(0, pad))
                ];
            this._imageBox = null;
            return Geom.pointsToSegmentsClosed(points);
        }

        _getLinesBottomGuided() {
            let rect = this._getBox(),
                points = [
                    rect.bottomLeft(),
                    rect.top().center(),
                    rect.bottomRight()
                ];
            this._imageBox = null;
            return Geom.pointsToSegments(points);
        }

        _getLinesSideGuided(side = 'left') {
            let rect = this._getBox(),
                pad = rect.width * 0.15;
            let points = [
                    rect.topLeft().sum(new Geom.Vect((side === 'left') ? pad : (rect.width - pad), 0)),
                    (side === 'left' ? rect.right() : rect.left()).center(),
                    rect.bottomLeft().sum(new Geom.Vect((side === 'left') ? pad : (rect.width - pad), 0))
                ];
            this._imageBox = null;
            return Geom.pointsToSegmentsClosed(points);
        }

        _getLinesTopSwing() {
            let points1 = this._getLinesTopGuided(),
                points2 = this._getLinesBottomGuided();
            this._imageBox = null;
            return points1.concat(points2);
        }

        _getLinesSideSlide() {
            let rect = this._getBox(),
                points = [
                    rect.top().center(),
                    rect.right().center(),
                    rect.bottom().center()
                ];
            this._imageBox = null;
            return Geom.pointsToSegmentsClosed(points).concat(this._getLinesHinged('right'));
        }

        _getLinesFireEscape() {
            let points = this._getLinesTopGuided();
            this._imageBox = this._initImageBox();
            return points;
        }

        _getLinesTiltSlide(direction) {
            let rect = this._getBox();
            let padTop = rect.height * 0.5,
                arrowHeight = rect.width * 0.1,
                arrowLength = arrowHeight / 2,
                padSide = rect.width * 0.45 - arrowLength,
                padArrow = rect.width * 0.45;

            switch (direction) {
                case 'left': {
                    let arrowPoint = rect.topRight().sum(new Geom.Vect(-padArrow, padTop)),
                        points = [
                            rect.bottomLeft().sum(new Geom.Vect(padSide, -padTop)),
                            arrowPoint
                        ],
                        arrowPoints = [
                            arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2)),
                            arrowPoint.sum(new Geom.Vect(0, -arrowHeight / 2)),
                            arrowPoint.sum(new Geom.Vect(arrowLength, 0)),
                            arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2))
                        ];
                    return Geom.pointsToSegments(points).concat(Geom.pointsToSegments(arrowPoints));
                }
                case 'right': {
                    let arrowPoint = rect.topLeft().sum(new Geom.Vect(padArrow, padTop)),
                        points = [
                            rect.bottomRight().sum(new Geom.Vect(-padSide, -padTop)),
                            arrowPoint
                        ],
                        arrowPoints = [
                            arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2)),
                            arrowPoint.sum(new Geom.Vect(0, -arrowHeight / 2)),
                            arrowPoint.sum(new Geom.Vect(-arrowLength, 0)),
                            arrowPoint.sum(new Geom.Vect(0, arrowHeight / 2))
                        ];
                    return Geom.pointsToSegments(points).concat(Geom.pointsToSegments(arrowPoints));
                }
            }
        }

        _initImageBox() {
            let imageBox = new Geom.Rect(new Geom.Vect(0, 0), 20, 20),
                box = this.parent.box;
            imageBox.pos.x = box.pos.x + (box.width - imageBox.width) / 2;
            imageBox.pos.y = box.pos.y + (box.height - imageBox.height) / 2;
            return imageBox;
        }

        // "F" symbol (not currently used)
        _getLinesNone() {
            let rect = this._getBox(),
                padVert = rect.height * 0.15,
                padHoriz = rect.width * 0.15;
            let points = [
                rect.bottomLeft().sum(new Geom.Vect(padHoriz, -padVert)),
                rect.topLeft().sum(new Geom.Vect(padHoriz, padVert)),
                rect.topRight().sum(new Geom.Vect(-padHoriz, padVert))
            ];
            let middle = points[0].between(points[1]),
                middleSegment = new Geom.Segment(
                    middle,
                    middle.sum(new Geom.Vect(rect.width / 2 - padHoriz)));
            return Geom.pointsToSegments(points).concat(middleSegment);
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: OpenType
    };

});
