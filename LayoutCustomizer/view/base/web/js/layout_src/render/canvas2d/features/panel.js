define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class PanelDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            // Set line Color
            {
                let color = feature.params.lineColor;
                if (color === null) {
                    color = feature.parent.borderColor;
                }
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                }
            }

            // Set line width
            {
                let lineWidth = feature.params.lineWidth;
                if (lineWidth === null) {
                    lineWidth = feature.parent.border;
                }
                if (lineWidth != null) {
                    ctx.lineWidth = lineWidth;
                }
            }

            Helper.drawRect(drawer, feature.getInnerRect());
            feature.getBevelLines().forEach(Helper.drawLine.bind(null, drawer));
        }
    }

    return {Drawer: PanelDrawer};
});
