define([
    '../../drawable',
    '../helper'
], function(DrawableDrawer, Helper) {

    class DimensionDrawer extends DrawableDrawer.Base {
        draw(drawer, measurement) {
            let extensionLines = measurement.getExtensionLines(),
                connectionLine = measurement.getConnectionLine(),
                epsilon = 0.0001, // TODO: move
                isZeroLength = (connectionLine.length() < epsilon),
                isCustomizable = measurement.isCustomizable,
                hasInput = (!drawer.editMode && isCustomizable),
                hasDrawnText = !hasInput && !isZeroLength;

            // Lines
            let lines = (hasInput || hasDrawnText)
                ? [...extensionLines]
                : [];
            if (!isZeroLength) {
                lines.push(connectionLine);
            }
            lines.forEach(function(line) {
                Helper.drawLine(drawer, line);
            });

            // Text
            if (hasDrawnText) {
                this._drawText(drawer, measurement, connectionLine);
            }
        }

        _drawText(drawer, measurement, connectionLine) {
            let value = measurement.getValue();
            if (value === null) return;

            let text = drawer.format.decimal(value);
            if (measurement.isCustomizable) {
                text += '*';
            }
            Helper.fillTextOverLine(drawer, connectionLine, text);
        }
    }

    return {Drawer: DimensionDrawer};
});
