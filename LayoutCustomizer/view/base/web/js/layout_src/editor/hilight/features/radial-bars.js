define([
    '../../../render/canvas2d/helper'
], function(DrawerHelper) {

    function hilight(drawer, feature) {
        // TODO: switch to using classes, move clipping to feature highlight base class
        drawer.context2d.save();
        if (feature.bounded) {
            let block = feature.parent,
                shapeDrawer = drawer.get('shape');
            shapeDrawer.clip(drawer, block.shape);
        }
        feature.getBorderLines().forEach(DrawerHelper.drawLine.bind(null, drawer));
        drawer.context2d.restore();
    }

    return {hilight: hilight};
});
