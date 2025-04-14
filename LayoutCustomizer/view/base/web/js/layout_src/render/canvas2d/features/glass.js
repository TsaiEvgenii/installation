define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class GlassDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            let color = feature.params.color;
            if (color !== null) {
                ctx.fillStyle = Color.prepare(color);
                let opacity = feature.params.opacity;
                if (opacity !== null) {
                    ctx.globalAlpha = opacity;
                }
                Helper.fillRect(drawer, feature.getRect());
            }
        }
    }

    return {Drawer: GlassDrawer};
});
