define([
    '../../../render/canvas2d/helper'
], function(Helper) {

    function hilight(drawer, measurement) {
        Helper.drawLine(drawer, measurement.getLine());
    }

    return {hilight: hilight};
})
