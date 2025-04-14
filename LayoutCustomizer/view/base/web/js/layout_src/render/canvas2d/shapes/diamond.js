define([
    './_polygon'
], function(PolygonDrawer) {

    class DiamondDrawer extends PolygonDrawer.Drawer {}

    return {Drawer: DiamondDrawer};
});
