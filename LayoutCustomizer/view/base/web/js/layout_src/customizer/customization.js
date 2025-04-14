define([
    './customization/all'
], function(Helpers) {

    class Customizer {
        constructor(context) {
            this._context = context;
        }

        customize(drawer, object) {
            if (object.drawableType
                && object.drawableType == 'measurement')
            {
                let helper = this._getHelper(object.measurementType);
                if (helper) {
                    helper.customize(this, drawer, object);
                }
            }
        }

        _getHelper(measurementType) {
            return Helpers[measurementType]
                ? Helpers[measurementType]
                : null;
        }

        get context() { return this._context; }
    }

    return {Customizer: Customizer};

});
