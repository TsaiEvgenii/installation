define([
    './helper/html'
], function(HtmlHelper) {

    class Widget {
        constructor(name, context, elementType, elementAttributes = {}) {
            this._name = name;
            this._context = context;
            this._parent = null;
            this._children = [];
            this._initElement(elementType, elementAttributes);
        }

        _initElement(type, attributes) {
            // make elemnet
            let element = this._context.elementFactory.make(type, attributes);
            // add class names
            this._name.split(/\s+/).forEach(
                HtmlHelper.addClassName.bind(null, element));
            this._element = element;
        }

        destroy() {
            this._children.forEach(function(child) { child.destroy(); });
            if (this._parent) {
                this._parent.remove(this);
            } else {
                this._element.parentNode.removeChild(this._element);
            }
        }

        update() {
            this._update();
            this._children.forEach(function(child) { child.update(); });
        }

        _update() {}

        add(widget) {
            widget.parent = this;
            this._children.push(widget);
        }

        remove(widget) {
            let idx = this._children.indexOf(widget);
            if (idx != -1) {
                widget.element.parentNode.removeChild(widget.element);
                this._children.splice(idx, 1)[0];
            }
        }

        hide() {
            this._element.style.display = 'none';
        }

        show() {
            this._element.style.display = '';
        }

        toggle(visible) {
            if (visible === undefined)
                visible = !this.isVisible();
            visible ? this.show() : this.hide();
        }

        isVisible() {
            return this._element.style.display != 'none';
        }

        get name() { return this._name; }

        get context() { return this._context; }

        get element() { return this._element }

        get parent() { return this._parent; }
        set parent(parent) { this._parent = parent; }

        get children() { return this._children; }
    }

    return {Base: Widget};
});
