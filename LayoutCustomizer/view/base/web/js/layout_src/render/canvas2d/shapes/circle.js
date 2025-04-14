define([
    '../../../blocks/color',
    '../shape',
    '../helper'
], function(Color, ShapeDrawer, Helper) {

    class CircleDrawer extends ShapeDrawer.Base {
        _draw(drawer, shape) {
            let ctx = drawer.context2d,
                block = shape.parent;

            // Fill background
            if (block.color != null) {
                ctx.save();
                ctx.fillStyle = Color.prepare(block.color);
                Helper.fillCircle(drawer, shape.getPaddingCircle());
                ctx.restore();
            }

            // Draw border
            if (block.borderColor != null && block.border > 0) {
                ctx.save();
                ctx.lineWidth = block.border;
                ctx.strokeStyle = Color.prepare(block.borderColor);
                Helper.drawCircle(drawer, shape.getBorderCircle());
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
                Helper.drawCircle(drawer, shape.getInnerBorderCircle());
                ctx.restore();
            }
        }

        clip(drawer, shape) {
            Helper.clipCircle(drawer, shape.getFeatureClipCircle());
        }
    }

    return {Drawer: CircleDrawer};
});
