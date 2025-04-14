define([
    '../../../blocks/geometry',
    '../../drawable',
    '../helper'
], function(Geom, DrawableDrawer, Helper) {

    class RadiusDrawer extends DrawableDrawer.Base {
        draw(drawer, measurement) {
            // Draw line
            Helper.drawLine(drawer, measurement.getLine());

            // Draw text
            if (drawer.editMode || !measurement.isCustomizable) {
                this._drawText(drawer, measurement, measurement.getLine());
            }
        }

        _drawText(drawer, measurement, line) {
            let text = drawer.format.decimal(measurement.getValue())
                + (measurement.isCustomizable ? '*' : '');
            Helper.fillTextOverLine(drawer, line, text);
        }
    }

    return {Drawer: RadiusDrawer};
});
