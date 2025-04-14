define([
    '../../../blocks/color',
    '../shape',
    '../helper'
], function(Color, ShapeDrawer, Helper) {

    class PolygonDrawer extends ShapeDrawer.Base {
        constructor() {
            super();
            this._lineCap = 'round';
        }

        _draw(drawer, shape) {
            let ctx = drawer.context2d,
                block = shape.parent;

            if (!shape.canDraw()) {
                return;
            }

            // Fill background
            if (block.color != null) {
                ctx.save();
                ctx.fillStyle = Color.prepare(block.color);
                Helper.fillClosedPath(drawer, shape.getPaddingPath());
                ctx.restore();
            }

            // Draw border
            if (block.borderColor != null && block.border > 0) {
                ctx.save();
                ctx.lineWidth = block.border;
                ctx.strokeStyle = Color.prepare(block.borderColor);
                ctx.lineCap = this._lineCap;
                shape.getBorderLines().forEach(Helper.drawLine.bind(null, drawer));
                ctx.restore();
            }

            // Draw inner border
            let innerBorderColor = (block.innerBorderColor !== null)
                ? block.innerBorderColor
                : block.borderColor;
            let innerBorder = block.getInnerBorder();
            if (innerBorderColor != null && innerBorder > 0) {
                ctx.save();
                ctx.lineWidth = innerBorder;
                ctx.strokeStyle = Color.prepare(innerBorderColor);
                ctx.lineCap = this._lineCap;
                shape.getInnerBorderLines().forEach(Helper.drawLine.bind(null, drawer));
                ctx.restore();
            }

        }

        clip(drawer, shape) {
            Helper.clipClosedPath(drawer, shape.getFeatureClipPath());
        }
    }

    return {Drawer: PolygonDrawer};
});
