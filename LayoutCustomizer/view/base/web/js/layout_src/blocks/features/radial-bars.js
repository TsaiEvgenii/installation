define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'radial-bars',
        Name = 'Radial Bars',
        Params = {
            num: {label: 'Number', type: 'number'},
            angle: {label: 'Angle', type: 'number'},
            width: {label: 'Width', type: 'number'},
            centerHeight: {label: 'Center Height'},
            color: {label: 'Color', type: 'color', nullable: true},
            lineColor: {label: 'Line Color', type: 'color', nullable: true}
        };

    class RadialBars extends Feature.Base {
        constructor() {
            super(Type);
            // default params
            this.params.num = 8;
            this.params.angle = 0;
            this.params.width = 5;
            this.params.centerHeight = 0;
            this.params.color = null;
            this.params.lineColor = null;
            // calculated properties
            this._pathPoints = [];
            this._borderLines = [];
        }

        place() {
            let box = this.parent.shape.getFeatureBox(),
                num = Math.max(3, this.params.num),
                center = box.bottom().center().sum(new Geom.Vect(0, -this.params.centerHeight)),
                length = box.topRight().diff(box.bottomLeft()).magnitude(),
                centerVect = (new Geom.Vect(1, 0)).rotated(this.params.angle / 180 * Math.PI),
                stepAngle = Math.PI * 2 / num,
                radius = Math.max(0, this.params.width) / 2 / Math.cos(Math.PI / num);
            this._pathPoints = [];
            this._borderLines = [];
            for (let i = 0; i < num; ++i) {
                // Bar:
                //          _______length________
                //         |                     | 
                //      p4 +---------------------+ p3    centerVect
                // center *-)--------------------|------------->
                //      p1 +---------------------+ p2
                //
                // radius: radius of outer circle of regular polygon

                let p1 = center.sum(centerVect.rotated(-stepAngle/2).resized(radius)),
                    p2 = p1.sum(centerVect.resized(length)),
                    p4 = center.sum(centerVect.rotated(stepAngle/2).resized(radius)),
                    p3 = p4.sum(centerVect.resized(length));
                this._pathPoints = this._pathPoints.concat([p1, p2, p3, p4]);
                this._borderLines = this._borderLines.concat([
                    new Geom.Segment(p1, p2),
                    new Geom.Segment(p3, p4)
                ]);

                centerVect.rotate(stepAngle);
            }
        }

        getPathPoints() {
            return this._pathPoints;
        }

        getBorderLines() {
            return this._borderLines;
        }

        getColor() {
            return (this.params.color !== null)
                ? this.params.color
                : this.parent.color;
        }

        getLineColor() {
            return (this.params.lineColor !== null)
                ? this.params.lineColor
                : this.parent.borderColor;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: RadialBars
    };
});
