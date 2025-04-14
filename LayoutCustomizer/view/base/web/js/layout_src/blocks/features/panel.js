define([
    '../color',
    '../feature',
    '../geometry'
], function(Color, Feature, Geom) {

    var Type = 'panel',
        Name = 'Panel',
        Params = {
            bevel: {label: 'Bevel', type: 'number'},
            lineColor: {label: 'Line Color', type: 'color', nullable: true},
            lineWidth: {label: 'Line Width', type: 'number', nullable: true}
        };

    class Panel extends Feature.Base {
        constructor() {
            super(Type);
            // default params
            this.params.bevel = 10;
            this.params.lineColor = null;
            this.params.lineWidth = null;
            // calculated properties
            this._innerRect = new Geom.Rect();
            this._bevelLines = [];
        }

        place() {
            let box = this.parent.shape.getFeatureBox(),
                bevel = this.params.bevel;

            // inner rectangle
            this._innerRect = box.grown(new Geom.Vect(bevel, bevel).inversed());

            // bevel lines
            let methods = ['topLeft', 'topRight', 'bottomLeft', 'bottomRight'];
            this._bevelLines = methods.map(function(method) {
                return new Geom.Segment(box[method](), this._innerRect[method]());
            }, this);
        }

        getInnerRect() {
            return this._innerRect;
        }

        getBevelLines() {
            return this._bevelLines;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: Panel
    };
})
