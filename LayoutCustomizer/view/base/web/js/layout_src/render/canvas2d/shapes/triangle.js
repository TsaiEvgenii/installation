define([
    './_polygon'
], function(PolygonDrawer) {

    class TriangleDrawer extends PolygonDrawer.Drawer {}

    return {Drawer: TriangleDrawer};
});
