define([
    '../../ui/widget'
], function(Widget) {

    class Container extends Widget.Base {
        constructor(context, elementType = 'div', elementAttributes = {}) {
            super('container', context, elementType);
        }

        add(widget) {
            super.add(widget);
            this._element.appendChild(widget.element);
        }
    };

    return {Widget: Container}
});
