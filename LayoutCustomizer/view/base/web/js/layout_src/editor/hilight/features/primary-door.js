define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        DrawerHelper.drawRect(drawer, feature.getImageBox());
    }

    return {hilight: hilight};
});
