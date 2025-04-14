define([
    '../../../blocks/color',
    '../../../blocks/geometry',
    '../feature',
    '../helper'
], function(Color, Geom, FeatureDrawer, Helper) {

    class DoorHandleDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {
            let ctx = drawer.context2d,
                rect = feature.parent.innerBox(),
                circle = feature.getCircle(),
                handle = feature.getHandle(),
                lock = feature.getLock(),
                ellipse = feature.getEllipse(),
                radius = 2,
                roundedSides = feature.params.side;

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

            if(feature.params.type !== 'none') {
                Helper.fillCircle(drawer, circle);
                Helper.drawCircle(drawer, circle);

                switch(feature.params.type) {
                    case 'type-u':
                        Helper.drawRoundRect(drawer, handle, radius, roundedSides);
                        break;
                    case 'type-l':
                        Helper.fillRect(drawer, handle);
                        Helper.drawRect(drawer, handle);
                        break;
                    case 'type-c':
                        ctx.lineJoin = "round";
                        Helper.fillClosedPath(drawer, handle);
                        Helper.drawConnectedPoints(drawer, handle, true);
                        break;
                }

                if (feature.params.is_lock) {
                    Helper.fillCircle(drawer, lock);
                    Helper.drawCircle(drawer, lock);

                    Helper.drawEllipse(drawer, ellipse);
                }
            }
        }
    }
    return {Drawer: DoorHandleDrawer};
});
