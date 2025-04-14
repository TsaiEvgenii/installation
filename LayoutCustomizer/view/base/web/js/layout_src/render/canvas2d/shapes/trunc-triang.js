define([
    './_polygon'
], function(PolygonDrawer) {

    class TruncatedTriangularDrawer extends PolygonDrawer.Drawer {}

    return {Drawer: TruncatedTriangularDrawer};
});
