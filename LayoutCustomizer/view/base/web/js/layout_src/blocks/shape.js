define([
    './drawable'
], function(Drawable) {

    let Type = 'shape';

    class Shape extends Drawable.Base {
        constructor(type) {
            super(Type);
            this._shapeType = type;
            this._params = {}
        }

        getMeasurements() { return []; }

        prepare() {}

        place() {}

        getFeatureBox() { throw "`getFeatureBox()' is not implemented"; }

        getDimensionParams(placement) {
            return null;
        }

        get shapeType() { return this._shapeType; }

        get params() { return this._params; }
    }

    return {
        Type: Type,
        Base: Shape
    };
});
