define([
    '../../../blocks/color',
    '../shape',
    '../helper'
], function(Color, ShapeDrawer, Helper, Geom) {

    class SemicircleDrawer extends ShapeDrawer.Base {
        _draw(drawer, shape) {
            let ctx = drawer.context2d,
                block = shape.parent;

            // Fill background
            if (block.color != null) {
                ctx.save();
                ctx.fillStyle = Color.prepare(block.color);
                ctx.beginPath();
                Helper.addPath(drawer, shape.getPaddingLine().vertices());
                Helper.addArcPath(drawer, shape.getPaddingArc());
                ctx.closePath();
                ctx.fill();
                ctx.restore();
            }

            // Draw border
            if (block.borderColor != null && block.border > 0) {
                ctx.save();
                ctx.lineWidth = block.border;
                ctx.lineCap = 'round';
                ctx.strokeStyle = Color.prepare(block.borderColor);
                Helper.drawLine(drawer, shape.getBorderLine());
                Helper.drawArc(drawer, shape.getBorderArc());
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
                ctx.lineCap = 'round';
                ctx.strokeStyle = Color.prepare(innerBorderColor);
                Helper.drawLine(drawer, shape.getInnerBorderLine());
                Helper.drawArc(drawer, shape.getInnerBorderArc());
                ctx.restore();
            }
        }

        clip(drawer, shape) {
            let ctx = drawer.context2d;
            ctx.beginPath();
            Helper.addPath(drawer, shape.getFeatureClipLine().vertices());
            Helper.addArcPath(drawer, shape.getFeatureClipArc());
            ctx.closePath();
            ctx.clip();
        }
    }

    return {Drawer: SemicircleDrawer};
});
