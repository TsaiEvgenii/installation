define([
    './circle',
    './diamond',
    './rectangle',
    './semicircle',
    './triangle',
    './trunc-triang',
    './vert-triang'
], function(
    Circle,
    Diamond,
    Rectangle,
    Semicircle,
    Triangle,
    TruncatedTriangular,
    VerticalTriangular) {

    return {
        'circle': Circle,
        'diamond': Diamond,
        'rectangle': Rectangle,
        'semicircle': Semicircle,
        'triangle': Triangle,
        'trunc-triang': TruncatedTriangular,
        'vert-triang': VerticalTriangular
    };
});
