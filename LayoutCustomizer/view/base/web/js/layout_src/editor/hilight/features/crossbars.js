define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        feature.getLines().forEach(
            DrawerHelper.drawLine.bind(null, drawer));
    }

    return {hilight: hilight};
});
