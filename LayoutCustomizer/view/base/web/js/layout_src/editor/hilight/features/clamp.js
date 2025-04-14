define([
    '../../../render/canvas2d/helper',
    '../../../blocks/geometry'
], function(DrawerHelper, Geom) {

    function hilight(drawer, feature) {
        let pad = 2;
        if(feature._rectangle !== 0) {
            DrawerHelper.drawRect(drawer, feature._rectangle.grown(new Geom.Vect(pad, pad)));
        }

    }

    return {hilight: hilight}
})
