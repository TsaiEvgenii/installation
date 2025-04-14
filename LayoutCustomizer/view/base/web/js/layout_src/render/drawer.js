define([
    './format'
], function(Format) {

    class Drawer {
        constructor() {
            this._locale = 'en-US';
            this._numberPrecision = 1;
            this._format = new Format(this);
            this._scaleMultiplier = 1.0;
            this._scale = 1.0;
            this._fontFamily = null;
            this._fontSize = null;
            this._mobileFontSize = null;
            this._mobileBreakpoint = null;
            this._editMode = false;
            this._drawers = {};
            this._beforeDrawHandlers = [];
            this._afterDrawHandlers = [];
            this._assetUrls = {};
        }

        get(type) {
            return this._drawers[type] || null;
        }

        add(type, drawer) {
            this._drawers[type] = drawer;
        }

        addAssetUrl(id, url) {
            this._assetUrls[id] = url;
        }

        getAssetUrl(id) {
            return this._assetUrls[id] || null;
        }

        getPositioningData() {
            return {
                scale: this.scale,
                scaleMultiplier: this.scaleMultiplier
            };
        }

        setPositioningData(data) {
            this.scale = data.scale,
            this.scaleMultiplier = data.scaleMultiplier;
        }

        addBeforeDraw(handler) {
            this._beforeDrawHandlers.push(handler);
        }

        addAfterDraw(handler) {
            this._afterDrawHandlers.push(handler);
        }

        draw(object) {
            if (object.isRoot()) {
                this._center(object);
            }
            this._beforeDraw(object);
            this._draw(object);
            this._afterDraw(object);
        }

        exportImage() { throw "`exportImage()' is not implemented"; }

        getScale() {
            return this._scale * this._scaleMultiplier;
        }

        getFont() {
            return (this._fontFamily !== null && this._fontSize !== null)
                ? this.getCurrentFontSize() + 'px ' + this._fontFamily
                : null;
        }

        getCurrentFontSize() {
            return this._mobileBreakpoint && (window.innerWidth < this._mobileBreakpoint) ? this._mobileFontSize : this._fontSize;
        }

        resize(width, height) { throw "`resize()' is not implemented"; }

        _beforeDraw(object) {
            this._beforeDrawHandlers.forEach(function(handler) {
                handler(this, object);
            }, this);
        }

        _draw(object) {
            let drawer = this.get(object.drawableType);
            if (drawer) {
                drawer.draw(this, object);
            }
        }

        _afterDraw(object) {
            this._afterDrawHandlers.forEach(function(handler) {
                handler(this, object);
            }, this);
        }

        reset() {}

        _center(object) {}

        get locale() { return this._locale; }
        set locale(locale) { this._locale = locale; }

        get numberPrecision() { return this._numberPrecision; }
        set numberPrecision(numberPrecision) { this._numberPrecision = numberPrecision; }

        get format() { return this._format; }

        get scaleMultiplier() { return this._scaleMultiplier; }
        set scaleMultiplier(scaleMultiplier) { this._scaleMultiplier = scaleMultiplier; }

        get scale() { return this._scale; }
        set scale(scale) { this._scale = scale; }

        get fontFamily() { return this._fontFamily; }
        set fontFamily(fontFamily) { this._fontFamily = fontFamily; }

        get fontSize() { return this._fontSize; }
        set fontSize(fontSize) { this._fontSize = fontSize; }

        get editMode() { return this._editMode; }
        set editMode(editMode) { this._editMode = editMode; }

        get mobileFontSize() { return this._mobileFontSize; }
        set mobileFontSize(mobileFontSize) { this._mobileFontSize = mobileFontSize; }

        get mobileBreakpoint() { return this._mobileBreakpoint; }
        set mobileBreakpoint(mobileBreakpoint) { this._mobileBreakpoint = mobileBreakpoint; }
    }

    return {Base: Drawer};
});
