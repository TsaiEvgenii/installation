define([
    '../widget'
], function(Widget) {

    class ToolbarBase extends Widget.Base {
        constructor(context, toolList) {
            super('toolbar', context, 'div');
            this._toolList = toolList;
        }

        addTools(types) {
            types.forEach(this.addTool, this);
        }

        addTool(type) {
            if (!this._toolList[type]) {
                throw "Invalid tool `" + type + "'";
            }
            let tool = new this._toolList[type].Widget(this.context);
            this.add(tool);
        }

        add(widget) {
            super.add(widget);
            this.element.appendChild(widget.element);
        }
    }

    return {Base: ToolbarBase};
});
