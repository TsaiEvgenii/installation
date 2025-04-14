define([
    '../drawable'
], function(DrawableDrawer) {

    class FeatureDrawer extends DrawableDrawer.CompositeBase {
        _getObjectType(object) {
            return object.featureType;
        }
    }

    class FeatureDrawerBase extends DrawableDrawer.Base {
        beforeDraw(drawer, feature) {
            drawer.context2d.save();
            if (feature.bounded) {
                // Clip context region to block shape
                let block = feature.parent,
                    shapeDrawer = drawer.get('shape');
                shapeDrawer.clip(drawer, block.shape);
            }
        }

        afterDraw(drawer, feature) {
            drawer.context2d.restore();
        }
    }

    return {
        Drawer: FeatureDrawer,
        Base: FeatureDrawerBase
    };
});
