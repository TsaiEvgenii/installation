define([
    '../drawable'
], function(DrawableDrawer) {

    class MeasurementDrawer extends DrawableDrawer.CompositeBase {
        _getObjectType(object) {
            return object.measurementType;
        }
    }

    class MeasurementDrawerBase extends DrawableDrawer.Base {};

    return {
        Drawer: MeasurementDrawer,
        Base: MeasurementDrawerBase
    };
});
