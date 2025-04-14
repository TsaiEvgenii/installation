define([
    './drawable'
], function(Drawable) {

    var Type = 'feature';

    class Feature extends Drawable.Base {
        constructor(featureType) {
            super(Type);
            this._featureType = featureType;
            this._showOverChildren = false;
            this._bounded = true;
            this._params = {};
        }

        place() {}

        get featureType() { return this._featureType; }
        get bounded() { return this._bounded; }

        get showOverChildren() { return this._showOverChildren; }
        set showOverChildren(showOverChildren) {
            this._showOverChildren = showOverChildren; }

        get params() { return this._params; }
        set params(params) { this._params = params; }
    }

    return {
        Type: Type,
        Base: Feature
    };
});
