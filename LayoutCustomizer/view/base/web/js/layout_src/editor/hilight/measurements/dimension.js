define([
    '../../../render/canvas2d/helper'
], function(Helper) {

    function hilight(drawer, measurement) {
        let extensionLines = measurement.getExtensionLines(),
            connectionLine = measurement.getConnectionLine(),
            lines = [...extensionLines, connectionLine];
        lines.forEach(function(line) {
            Helper.drawLine(drawer, line);
        });
    }

    return {hilight: hilight};
});
