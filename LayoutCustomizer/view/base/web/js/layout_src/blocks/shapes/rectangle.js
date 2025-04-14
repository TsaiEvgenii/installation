define([
    './_polygon',
], function(Polygon) {

    let Type = 'rectangle',
        Name = 'Rectangle';

    class Rectangle extends Polygon.Shape {
        constructor() {
            super(Type);
        }

        _boxToPathPoints(box) {
            return box.vertices();
        }
    }

    return {
        Type: Type,
        Name: Name,
        Shape: Rectangle
    };
});
