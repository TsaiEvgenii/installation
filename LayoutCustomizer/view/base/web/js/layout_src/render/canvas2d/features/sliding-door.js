define([
    '../../../blocks/color',
    '../../../blocks/geometry',
    '../feature',
    '../helper'
], function(Color, Geom, FeatureDrawer, Helper) {

    class SlidingDoorDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {

        }
    }

    return {Drawer: SlidingDoorDrawer };
});
