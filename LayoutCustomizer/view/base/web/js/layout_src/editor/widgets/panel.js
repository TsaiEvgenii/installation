define([
    '../../ui/widget'
], function(Widget) {

    class Panel extends Widget.Base {
        constructor(context) {
            super('panel', context, 'div');
        }

        add(widget) {
            super.add(widget)
            this.element.appendChild(widget.element);
        }
    }

    return {Widget: Panel};
});
