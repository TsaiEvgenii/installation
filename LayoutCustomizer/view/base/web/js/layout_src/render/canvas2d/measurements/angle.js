define([
    '../../../blocks/geometry',
    '../../drawable',
    '../helper'
], function(Geom, DrawableDrawer, Helper) {

    class AngleDrawer extends DrawableDrawer.Base {
        draw(drawer, measurement) {
            // Clip to region set by (apex, point1) and (apex, point2) vectors,
            // draw a circle, remove clipping

            // Save context
            drawer.context2d.save();
            // Clip
            Helper.clipClosedPath(drawer, measurement.getClipPoints());
            // Draw circle
            Helper.drawCircle(drawer, measurement.getCircle());
            // Restor context
            drawer.context2d.restore();

            // Draw text
            if (drawer.editMode || !measurement.isCustomizable) {
                this._drawText(drawer, measurement, measurement.getTextLine());
            }
        }

        _drawText(drawer, measurement, textLine) {
            let text = drawer.format.decimal(measurement.getValue())
                + (measurement.isCustomizable ? '*' : '');
            Helper.fillTextOverLine(drawer, textLine, text);
        }
    }

    return {Drawer: AngleDrawer};
});
