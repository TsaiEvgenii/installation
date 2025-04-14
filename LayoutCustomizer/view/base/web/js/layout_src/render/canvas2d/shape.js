define([
    '../drawable'
], function(DrawableDrawer) {

    class ShapeDrawer extends DrawableDrawer.CompositeBase {
        _getObjectType(object) {
            return object.shapeType;
        }

        clip(drawer, shape) {
            let typeDrawer = this.get(this._getObjectType(shape));
            if (typeDrawer) {
                typeDrawer.clip(drawer, shape);
            }
        }
    }

    class ShapeDrawerBase extends DrawableDrawer.Base {
        clip(drawer, shape) {}
    }

    return {
        Drawer: ShapeDrawer,
        Base: ShapeDrawerBase
    };
});
