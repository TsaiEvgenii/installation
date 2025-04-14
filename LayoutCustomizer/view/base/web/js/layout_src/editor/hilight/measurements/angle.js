define([
    '../../../render/canvas2d/helper'
], function(Helper) {

    function hilight(drawer, measurement) {
        drawer.context2d.save();
        Helper.clipClosedPath(drawer, measurement.getClipPoints());
        Helper.drawCircle(drawer, measurement.getCircle());
        drawer.context2d.restore();
    }

    return {hilight: hilight};
});
