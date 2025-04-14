define([
    '../geometry',
    '../feature'
], function(Geom, Feature) {

    var Type = 'bevel',
        Name = 'Bevel',
        Params = {
            lineColor: {label: 'Line Color', type: 'color', nullable: true},
            lineWidth: {label: 'Line Width', type: 'number', nullable: true}
        };

    class Bevel extends Feature.Base {
        constructor() {
            super(Type);
            this._bounded = false;
            // default params
            this.params.lineColor = null;
            this.params.lineWidth = null;
            // calculated properties
            this._lines = [];
        }

        place() {
            let block = this.parent;
            let outerPad = block.borderPad() - this.getLineWidth() / 2;
            let outerVertices = block.box
                .grown(new Geom.Vect(outerPad, outerPad))
                .vertices();
            let innerPad = block.innerBorderPad() + this.getLineWidth() / 2;
            let innerVertices = block.innerBox()
                .grown(new Geom.Vect(innerPad, innerPad))
                .vertices();
            this._lines = [];
            for (let i = 0; i < outerVertices.length; ++i) {
                let line = new Geom.Segment(outerVertices[i], innerVertices[i]);
                this._lines.push(line);
            }
        }

        getLines() {
            return this._lines;
        }

        getLineColor() {
            return (this.params.lineColor !== null)
                ? this.params.lineColor
                : this.parent.borderColor;
        }

        getLineWidth() {
            return (this.params.lineWidth !== null)
                ? this.params.lineWidth
                : this.parent.border;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: Bevel
    };
});
