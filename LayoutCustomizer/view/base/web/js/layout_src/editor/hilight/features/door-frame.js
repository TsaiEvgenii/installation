define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        DrawerHelper.drawConnectedPoints(drawer, feature.getBorderPoints());
    }

    return {hilight: hilight};
});
