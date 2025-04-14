define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class BevelDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            let color = feature.getLineColor(),
                width = feature.getLineWidth();
            if (color !== null && width > 0) {
                ctx.fillStyle = Color.prepare(color);
                ctx.lineCap = 'round';
                ctx.lineWidth = width;
                feature.getLines().forEach(Helper.drawLine.bind(null, drawer));
            }
        }
    }

    return {Drawer: BevelDrawer};
})
