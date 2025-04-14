define([
    '../drawable'
], function(DrawableDrawer) {

    let Type = 'block';

    class BlockDrawer extends DrawableDrawer.Base {
        _draw(drawer, block) {
            let ctx = drawer.context2d;

            // Shape
            drawer.draw(block.shape);

            // Features below children
            block.features
                .filter(function(feature) { return !feature.showOverChildren; })
                .forEach(drawer.draw, drawer);

            // Children
            block.children.forEach(drawer.draw, drawer);

            // Features above children
            block.features
                .filter(function(feature) { return feature.showOverChildren; })
                .forEach(drawer.draw, drawer);

            // Measurements
            // block.measurements.forEach(drawer.draw, drawer);
            if (block.isRoot()) {
                (block.objectData._measurements || []).forEach(drawer.draw, drawer);
            }
        }
    }

    return {
        Type: Type,
        Drawer: BlockDrawer
    };
});
