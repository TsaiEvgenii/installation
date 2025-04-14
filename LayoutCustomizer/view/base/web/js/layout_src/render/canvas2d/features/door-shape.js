define([
    '../../../blocks/color',
    '../feature',
    '../helper',
    '../../../blocks/geometry'
], function(Color, FeatureDrawer, Helper, Geom) {

    class DoreShapeDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
        }

        _draw(drawer, feature) {
            let ctx = drawer.context2d,
                rect = feature.parent.innerBox(),
                startPoint = feature._startPoint,
                endPoint = feature._endPoint,
                bezierLines = feature._bezierLines,
                shapeWidth = feature._shapeWidth,
                shapeHeight = feature._shapeHeight,
                sinHeight = feature._sinHeight,
                squares = feature._squares,
                rectangles = feature._rectangles,
                bars = feature._bars,
                lines = feature._lines,
                offX = feature._offsetX,
                offY = feature._offsetY;

            let lineLength = feature._lineLength;

            // Set line Color
            {
                let color = feature.getLineColor();
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                }
            }

            // Set line width
            {
                let lineWidth = feature.getLineWidth();
                if (lineWidth != null) {
                    ctx.lineWidth = lineWidth;
                }
            }

            // Set line cap
            ctx.lineCap = 'round';

            ctx.fillStyle = feature.params.color;
            ctx.globalAlpha = feature.params.opacity;

            if(feature.params.type == 'semicircle') {
                bezierLines.forEach(Helper.drawCurve.bind(null, drawer, startPoint, endPoint, offX, offY));
            } else if(feature.params.type == 'curved') {
                bezierLines.forEach(Helper.drawCurveShape.bind(null, drawer, startPoint, endPoint, offX, offY, lineLength));
            } else if(feature.params.type == 'squares') {
                squares.forEach(function (square) {
                    Helper.drawRect(drawer, square);
                    square.grow(new Geom.Vect(-ctx.lineWidth/2, -ctx.lineWidth/2));
                    Helper.fillRect(drawer, square);
                });
            } else if(feature.params.type == 'rectangle-crossbars') {
                Helper.drawRectWithCrossbars(drawer, rectangles, bars);
            } else if(feature.params.type == 'sin-rectangle') {
                Helper.drawSinRectangleShape(drawer, startPoint, sinHeight, shapeWidth, shapeHeight);
            } else if(feature.params.type == 'crosslines') {
                lines.forEach((line) => {
                    Helper.drawLine(drawer, line);
                });
            } else {
                Helper.drawRect(drawer, feature._rectangle);
                let newRect = feature._rectangle.copy();
                newRect.grow(new Geom.Vect(-ctx.lineWidth/2, -ctx.lineWidth/2));
                Helper.fillRect(drawer, new Geom.Rect(newRect._pos, newRect.width, newRect.height));
            }
        }
    }

    return {Drawer: DoreShapeDrawer};
});
