define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class ClampDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {
            let ctx = drawer.context2d,
                rect = feature.parent.innerBox(),
                lines = feature.getLines();

            let lineLength = feature._lineLength;

            // Set line Color
            {
                let color = feature.getLineColor();
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                }
            }

            // Set line width
            {
                let lineWidth = feature.getLineWidth();
                if (lineWidth != null) {
                    ctx.lineWidth = lineWidth;
                }
            }

            // Set line cap
            ctx.lineCap = 'round';

            ctx.fillStyle = Color.prepare(feature.parent.color);
            ctx.globalAlpha = feature.params.opacity;

            if(feature._rectangle !== 0) {
                Helper.drawRect(drawer, feature._rectangle);
                Helper.fillRect(drawer, feature._rectangle);
                if (feature.params.type == 'double-clamp' && feature._separateLine) {
                    Helper.drawLine(drawer, feature._separateLine);
                }
            }
        }
    }

    return {Drawer: ClampDrawer};
});
