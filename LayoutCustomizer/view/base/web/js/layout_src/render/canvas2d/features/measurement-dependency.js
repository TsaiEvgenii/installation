define([
    '../feature',
], function(FeatureDrawer) {

    class MeasurementDependencyDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {}

    }

    return {Drawer: MeasurementDependencyDrawer};
});
