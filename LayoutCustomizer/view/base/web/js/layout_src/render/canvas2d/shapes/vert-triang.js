define([
    './_polygon'
], function(PolygonDrawer) {

    class VerticalTriangularDrawer extends PolygonDrawer.Drawer {}

    return {Drawer: VerticalTriangularDrawer};
});
