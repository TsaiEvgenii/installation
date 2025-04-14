define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        DrawerHelper.shadeRect(drawer, feature.getRect());
    }

    return {hilight: hilight}
});
