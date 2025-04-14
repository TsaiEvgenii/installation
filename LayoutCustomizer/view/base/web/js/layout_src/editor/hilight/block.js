define([
    '../../blocks/geometry',
    '../../render/canvas2d/helper'
], function(Geom, DrawerHelper) {

    function hilight(drawer, block) {
        let pad = block.borderOut();
        DrawerHelper.drawRect(drawer, block.outerBox().grown(new Geom.Vect(pad, pad)));
    }

    return {hilight: hilight};
});
