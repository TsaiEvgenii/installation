define([
    '../geometry',
    './_polygon'
], function(Geom, Polygon) {

    let Type = 'diamond',
        Name = 'Diamond',
        Options = {};

    class Diamond extends Polygon.Shape {
        constructor() {
            super(Type);
            this.params.height = null;
            this.params.width = null;

            this.params.min_dist = 15;
            this.params.reduce_value = 10;
        }

        _boxToPathPoints(box) {
            let parentWidth = this.getParentDimension('width'),
                dist = this.params.width ? (parentWidth - this.params.width) / 2 : 0,
                width = this.params.width,
                height = this.params.height;
            if(dist < this.params.min_dist) {
                width -= this.params.reduce_value;
                box.height = width;
            }
            if(this.params.width === this.params.height) height = width;


            let resizedBox = box.grown(
                new Geom.Vect(
                    this.params.width ? (width - box.width) / 2 : 0,
                    this.params.height ? (height - box.height) / 2 : 0,
                ));
            return [
                resizedBox.bottom().center(),
                resizedBox.left().center(),
                resizedBox.top().center(),
                resizedBox.right().center()
            ];
        }

        getParentDimension(dimension) {
            let parentBlock = this.parent,
                width = 0, adj = 0;
            while(width === 0) {
                if(parentBlock.width) width = parentBlock.width;
                else parentBlock = parentBlock.parent;
            }
            if(parentBlock.getMeasurementByDimension(dimension)) {
                adj = parentBlock.getMeasurementByDimension(dimension)._getAdjustmentSum();
            }
            return width + adj;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Shape: Diamond
    };
});
