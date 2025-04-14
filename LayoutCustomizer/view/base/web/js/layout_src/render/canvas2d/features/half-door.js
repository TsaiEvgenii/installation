/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    '../feature',
], function(FeatureDrawer) {

    class HalfDoorDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {}

    }

    return {Drawer: HalfDoorDrawer};
});
