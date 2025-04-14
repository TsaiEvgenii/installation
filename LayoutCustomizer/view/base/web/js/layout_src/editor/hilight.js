define([
    '../data/helper',
    './hilight/all'
], function(DataHelper, Helpers) {

    class Hilighter {
        constructor(context) {
            this._context = context;
        }

        hilight(drawer, object) {
            if (this._isObjectSelected(object)) {
                this.beforeHilight(drawer, object);
                this._hilight(drawer, object);
                this.afterHilight(drawer, object);
            }
        }

        beforeHilight(drawer, object) {
            this._savedContextParams = DataHelper.getFields(
                drawer.context2d,
                ['strokeStyle', 'lineWidth', 'lineDash'])
        }

        _hilight(drawer, object) {
            if (this._isObjectSelected(object)) {
                let helper = Helpers[object.drawableType];
                if (helper) {
                    // Set context parameters
                    DataHelper.setFields(drawer.context2d, {
                        strokeStyle: this._getStrokeStyle(),
                        lineWidth: this._getLineWidth(),
                        lineDash: []
                    });
                    // Hilight
                    helper.hilight(drawer, object);
                }
            }
        }

        afterHilight(drawer, object) {
            DataHelper.setFields(drawer.context2d, this._savedContextParams);
        }

        _isObjectSelected(object) {
            let objectId = null;
            if (object.objectId) {
                objectId = object.objectId;
            } else if (object.hilightObjectId) {
                objectId = object.hilightObjectId;
            }
            return objectId && this._context.selectedObjectIds.has(objectId);
        }

        _getStrokeStyle() {
            return this._getConfig('color');
        }

        _getLineWidth() {
            return this._getConfig('width');
        }

        _getConfig(param) {
            let config = this._context.config.Hilight;
            return config[param];
        }
    }

    return {Hilighter: Hilighter};
});
