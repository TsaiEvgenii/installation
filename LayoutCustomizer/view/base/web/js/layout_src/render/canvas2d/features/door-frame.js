define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class DoorFrameDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            // Fill
            {
                let color = feature.params.color;
                if (color === null) {
                    color = feature.parent.color;
                }
                if (color !== null) {
                    ctx.fillStyle = Color.prepare(Color.prepare(color));
                    Helper.fillClosedPath(drawer, feature.getFillPoints());
                }
            }

            // Border
            {
                let color = feature.params.lineColor;
                if (color == null) {
                    color = feature.parent.borderColor;
                }
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                    ctx.lineWidth = feature.getLineWidth();
                    Helper.drawConnectedPoints(drawer, feature.getBorderPoints());
                }
            }
        }
    }

    return {Drawer: DoorFrameDrawer};
});
