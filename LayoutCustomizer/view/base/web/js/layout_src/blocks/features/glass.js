define([
    '../color',
    '../feature',
    '../geometry'
], function(Color, Feature, Geom) {

    var Type = 'glass',
        Name = 'Glass',
        Params = {
            color: {label: 'Color', type: 'color'},
            opacity: {label: 'Opacity', type: 'number'}
        };

    class Glass extends Feature.Base {
        constructor() {
            super(Type);
            this.params.color = '#c1e7f8';
            this.params.opacity = 0.5;
            this._rect = new Geom.Rect();
        }

        place() {
            this._rect = this.parent.shape.getFeatureBox();
        }

        getRect() {
            return this._rect;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: Glass
    };
});
