define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'clamp',
        Name = 'Clamp',
        Params = {
            type: {label: 'Type', type: 'select', options: [
                {name: 'None', value: 'none'},
                {name: 'Clamp', value: 'clamp'},
                {name: 'Double clamp', value: 'double-clamp'}
            ]},
            side: { label: "Side", type: "select", options: [
                {name: "Top", value: "top"},
                {name: "Bottom", value: "bottom"}
            ]},
            height: { label: "Clamp Height", type: "number"},
            lineColor: {label: 'Line Color', type: 'color'}
        };

    class Clamp extends Feature.Base {
        constructor() {
            super(Type);
            this.params.type = 'clamp';
            this.params.side = 'bottom';
            this._lines = [];

            this.params.lineWidth = null;
            this.params.color = '#fff';
            this.params.height = 10;
        }

        place() {
            this._lines = this._getLines();
        }

        _getLines() {
            switch (this.params.type) {
                case 'none': {
                    this._rectangle = 0;
                    return [];
                }
                case 'clamp':
                    return this._getClampRect(this.params.side);
                case 'double-clamp':
                    return this._getClampRect(this.params.side, true);
                default:
                    throw "Invalid type: `" + this.params.type + "'";
            }
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

        _getBox() {
            let halfLineWidth = this.getLineWidth() / 2;
            return this.parent.shape.getFeatureBox()
                .grown(new Geom.Vect(-halfLineWidth, -halfLineWidth));
        }

        _getClampRect(direction, double = false) {
            this._lines = [];
            this._lineLength = 0;
            let rect = this._getBox(),
                rectHeight =  double ? 2*this.params.height : this.params.height,
                rectWidth = rect.width;

            switch (direction) {
                case 'top': {
                    this._rectangle = new Geom.Rect(rect.topLeft(), rectWidth, rectHeight);
                    if(double) {
                        let line = {};
                        line.p1 = new Geom.Vect(rect.topLeft().x, rect.topLeft().sum(new Geom.Vect(0, rectHeight/2)).y);
                        line.p2 = new Geom.Vect(rect.topRight().x, rect.topLeft().sum(new Geom.Vect(0, rectHeight/2)).y);
                        this._separateLine = line;
                    }
                    break;
                }
                case 'bottom': {
                    this._rectangle = new Geom.Rect(rect.bottomLeft().sum(new Geom.Vect(0, -rectHeight)), rectWidth, rectHeight);
                    if(double) {
                        let line = {};
                        line.p1 = new Geom.Vect(rect.bottomLeft().x, rect.bottomLeft().sum(new Geom.Vect(0, -rectHeight/2)).y);
                        line.p2 = new Geom.Vect(rect.bottomRight().x, rect.bottomLeft().sum(new Geom.Vect(0, -rectHeight/2)).y);
                        this._separateLine = line;
                    }
                    break;
                }
            }
        }

    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: Clamp
    };
});
