define(function() {

    class DrawableDrawer {
        draw(drawer, object) {
            this.beforeDraw(drawer, object);
            this._draw(drawer, object);
            this.afterDraw(drawer, object);
        }

        beforeDraw() {}
        afterDraw() {}
        _draw(drawer, object) {}
    }

    class CompositeDrawableDrawer extends DrawableDrawer {
        constructor(drawerList) {
            super();
            this._drawers = {};
            this.init(drawerList || {});
        }

        init(drawerList) {
            for (let type in drawerList) {
                this.add(type, new drawerList[type].Drawer());
            }
        }

        get(type) {
            return this._drawers[type] || null;
        }

        add(type, drawer) {
            this._drawers[type] = drawer;
        }

        _draw(drawer, object) {
            let type = this._getObjectType(object),
                typeDrawer = this.get(type);
            if (typeDrawer) {
                typeDrawer.draw(drawer, object);
            }
        }

        _getObjectType(object) {
            throw "Not implemented";
        }
    }

    return {
        Base: DrawableDrawer,
        CompositeBase: CompositeDrawableDrawer
    };
});
