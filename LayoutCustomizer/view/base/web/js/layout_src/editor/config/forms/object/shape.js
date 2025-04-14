define([
    './shapes/circle',
    './shapes/diamond',
    './shapes/rectangle',
    './shapes/semicircle',
    './shapes/triangle',
    './shapes/trunc-triang',
    './shapes/vert-triang',
], function(
    Circle,
    Diamond,
    Rectangle,
    Semicircle,
    Triangle,
    TruncatedTriangular,
    VerticalTriangular) {

    return {
        circle: Circle,
        diamond: Diamond,
        rectangle: Rectangle,
        semicircle: Semicircle,
        triangle: Triangle,
        'trunc-triang': TruncatedTriangular,
        'vert-triang': VerticalTriangular
    };
})
