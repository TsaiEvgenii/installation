define([
    './element',
    './event'
], function(Element, Ivent) {

    class Context {
        constructor(rootElement, config) {
            this._rootElement = rootElement;
            this._config = config;

            this._elementFactory = new Element.Factory();
            this._eventManager = new Ivent.Manager();
            this._scale = 1.0;
        }

        get config() { return this._config; }

        get rootElement() { return this._rootElement; }

        get elementFactory() { return this._elementFactory; }
        get eventManager() { return this._eventManager; }

        get scale() { return this._scale; }
        set scale(scale) { this._scale = scale; }
    }

    return Context;
});
