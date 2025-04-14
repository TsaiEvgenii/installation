define([
    './_polygon'
], function(PolygonDrawer) {

    class RectangleDrawer extends PolygonDrawer.Drawer {
        constructor() {
            super();
            this._lineCap = 'square';
        }
    }

    return {Drawer: RectangleDrawer};
});
