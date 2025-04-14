define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        let ctx = drawer.context;
        DrawerHelper.drawRect(drawer, feature.getInnerRect());
        feature.getBevelLines().forEach(DrawerHelper.drawLine.bind(null, drawer));
    }

    return {hilight: hilight}
})
