define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class RadialBarsDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            // Background
            {
                let color = feature.getColor();
                if (color !== null) {
                    ctx.fillStyle = Color.prepare(color);
                    Helper.fillClosedPath(drawer, feature.getPathPoints());
                }
            }

            // Border lines
            {
                let color = feature.getLineColor();
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                    feature.getBorderLines().forEach(Helper.drawLine.bind(null, drawer));
                }
            }
        }
    }

    return {Drawer: RadialBarsDrawer};
});
