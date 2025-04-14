define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class CrossbarsDrawer extends FeatureDrawer.Base {
        _draw(drawer, feature) {
            let ctx = drawer.context2d;

            // Background
            ctx.beginPath();
            // add box path, CCW
            Helper.addPath(drawer, feature.getBox().vertices().reverse());
            ctx.closePath();
            // add hole paths, CW -- opposite direction for cutting holes
            feature.getHoles().forEach(function(hole) {
                Helper.addPath(drawer, hole.vertices());
                ctx.closePath();
            });
            // fill
            {
                let color = feature.getColor();
                if (color !== null) {
                    ctx.fillStyle = Color.prepare(color);
                    ctx.fill();
                }
            }

            // Border lines
            {
                let color = feature.getLineColor();
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                    feature.getLines().forEach(Helper.drawLine.bind(null, drawer));
                }
            }
        }
    }

    return {Drawer: CrossbarsDrawer};
});
