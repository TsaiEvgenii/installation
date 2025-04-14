define([
    '../../blocks/geometry'
], function(Geom) {

    // TODO: refactor, use Path2D

    function scaleVect(drawer, vect) {
        return drawer.offset.sum(vect.product(drawer.getScale()));
    }

    function scaleVectList(drawer, list) {
        return list.map(scaleVect.bind(null, drawer));
    }

    function scaleDist(drawer, dist) {
        return dist * drawer.getScale();
    }

    function scaleSegment(drawer, segment) {
        return new Geom.Segment(
            scaleVect(drawer, segment.p1),
            scaleVect(drawer, segment.p2));
    }

    function stroke(drawer) {
        let ctx = drawer.context2d,
            lineWidth = ctx.lineWidth;
        ctx.lineWidth = scaleDist(drawer, ctx.lineWidth);
        ctx.stroke();
        ctx.lineWidth = lineWidth;
    }


    function addToPath(drawer, points) {
        scaleVectList(drawer, points).forEach(function(point) {
            drawer.context2d.lineTo(point.x, point.y);
        });
    }

    function addPath(drawer, points) {
        if (points.length > 0) {
            let point = scaleVect(drawer, points[0]);
            drawer.context2d.moveTo(point.x, point.y);
            addToPath(drawer, points.slice(1));
        }
    }

    function addCirclePath(drawer, circle) {
        let center = scaleVect(drawer, circle.center),
            radius = scaleDist(drawer, circle.radius);
        drawer.context2d.arc(center.x, center.y, radius, 0, Math.PI * 2);
    }

    function addArcPath(drawer, arc) {
        let center = scaleVect(drawer, arc.center),
            radius = scaleDist(drawer, arc.radius);
        drawer.context2d.arc(center.x, center.y, radius, arc.startAngle, arc.endAngle);
    }

    function drawConnectedPoints(drawer, points, closed = false) {
        let ctx = drawer.context2d;
        if (points.length > 1) {
            ctx.beginPath();
            addPath(drawer, points);
            if(closed) ctx.closePath();
            stroke(drawer);
        }
    }

    function fillClosedPath(drawer, points) {
        let ctx = drawer.context2d;
        if (points.length > 2) {
            ctx.beginPath();
            addPath(drawer, points);
            ctx.closePath();
            ctx.fill();
        }
    }

    function clipClosedPath(drawer, points) {
        let ctx = drawer.context2d;
        if (points.length > 2) {
            ctx.beginPath();
            addPath(drawer, points);
            ctx.closePath();
            ctx.clip();
        }
    }

    // Rect

    function drawRect(drawer, rect) {
        let ctx = drawer.context2d,
            lineCap = ctx.lineCap;
        ctx.lineCap = 'square';
        rect.sides().forEach(drawLine.bind(null, drawer));
        ctx.lineCap = lineCap;
    }

    function fillRect(drawer, rect) {
        let pos = scaleVect(drawer, rect.pos),
            width = scaleDist(drawer, rect.width),
            height = scaleDist(drawer, rect.height);
        drawer.context2d.fillRect(pos.x, pos.y, width, height);
    }

    function shadeRect(drawer, rect) {
        drawRect(drawer, rect);
        drawConnectedPoints(drawer, [rect.topLeft(), rect.bottomRight()]);
        drawConnectedPoints(drawer, [rect.topRight(), rect.bottomLeft()]);
    }


    // Line

    function drawLine(drawer, line) {
        drawConnectedPoints(drawer, [line.p1, line.p2]);
    }

    // Curve
    function drawCurve(drawer, startPoint, endPoint, offX, offY, lines) {
        addCurve(drawer, startPoint, endPoint, offX, offY, lines);
    }
    function drawCurveShape(drawer, startPoint, endPoint, offX, offY, lineLength, lines) {
        addCurve(drawer, startPoint, endPoint, offX, offY, lines, lineLength);
    }

    function addCurve(drawer, startPoint, endPoint,  offX, offY, lines, lineLength = false) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        addCurvePath(drawer, startPoint, endPoint, offX, offY, lines, lineLength);
        ctx.closePath();
        ctx.stroke();
        ctx.fill();
    }

    function addCurvePath(drawer, startPoint, endPoint, offX, offY, lines, lineLength = false) {
        let point = scaleVect(drawer, startPoint),
            pointEnd = scaleVect(drawer, endPoint);
        drawer.context2d.moveTo(point.x, point.y);

        drawer.context2d.bezierCurveTo(
            point.x + offX,
            point.y + offY,
            point.x + offX,
            pointEnd.y - offY,
            point.x,
            pointEnd.y
        );
        if(lineLength) {
            drawer.context2d.lineTo(point.x + lineLength, pointEnd.y);
            // drawer.context2d.moveTo(point.x + lineLength, point.y);
            drawer.context2d.bezierCurveTo(
                point.x + lineLength + offX,
                pointEnd.y - offY,
                point.x + lineLength + offX,
                point.y + offY,
                point.x + lineLength,
                point.y
            );
            drawer.context2d.lineTo(point.x, point.y);
        }
    }


    function drawSinRectangleShape(drawer, startPoint, sinHeight, width, height) {
        let ctx = drawer.context2d;
        let sinH = sinHeight;
        startPoint = scaleVect(drawer, startPoint);
        width = scaleDist(drawer, width);
        height = scaleDist(drawer, height);

        let topShapeH = height * 0.7,
            bottomShapeH = height * 0.2,
            space = height - topShapeH - bottomShapeH;

        drawSinRectangle(drawer, startPoint, sinH, width, topShapeH, 0,true);
        ctx.fill();
        drawSinRectangle(drawer, {x: startPoint.x, y: startPoint.y + topShapeH + space}, sinH, width, bottomShapeH);
        let innerOffset = 6;
        drawSinRectangle(drawer, {x: startPoint.x, y: startPoint.y + topShapeH + space}, sinH, width, bottomShapeH, innerOffset);

        let barWidth = 6,
            barStartPoint = { x: startPoint.x + ((width - barWidth)/2), y: startPoint.y },
            barGap = (topShapeH - 2*sinH - 2*barWidth) / 3,
            defaultGlobalCompositeOperation = ctx.globalCompositeOperation,
            defaultGlobalAlpha = ctx.globalAlpha;

        //draw stroked bars
        drawSinBars(drawer, barStartPoint, startPoint, sinH, width, barWidth, barGap, topShapeH - sinH*2);
        //fill bars
        ctx.globalCompositeOperation='destination-out';
        ctx.fillStyle = '#f4f5f5';
        ctx.globalAlpha = 1;
        drawSinBars(drawer, barStartPoint, startPoint, sinH, width, barWidth, barGap, topShapeH - sinH*2, true);

        ctx.globalCompositeOperation = defaultGlobalCompositeOperation;
        ctx.globalAlpha = defaultGlobalAlpha;
    }

    function drawSinRectangle(drawer, startPoint, sinH, width, height, offset = 0, bothSin = false) {
        let ctx = drawer.context2d;
        startPoint.y += offset;
        startPoint.x += offset;
        width -= offset*2;
        height -= offset*2;
        ctx.beginPath();
        ctx.moveTo(startPoint.x, startPoint.y + sinH*2 + offset);
        addSinPath(drawer, {x: startPoint.x, y: startPoint.y}, sinH, width);
        let y = bothSin ? startPoint.y + height - sinH*2 : startPoint.y + height;
        ctx.lineTo(startPoint.x + width, y);
        if(bothSin)
            addSinPath(drawer, {x: startPoint.x + width, y: y}, sinH, width, true);
        else
            ctx.lineTo(startPoint.x, y);
        ctx.closePath();
        ctx.stroke();
    }

    function drawSinBars(drawer, barStartPoint, startPoint, sinH, width, barWidth, barGap, barHeight, fillShape = false) {
        let ctx = drawer.context2d;
        if(fillShape) ctx.fillRect(barStartPoint.x, barStartPoint.y, barWidth, barHeight);
        else ctx.strokeRect(barStartPoint.x, barStartPoint.y, barWidth, barHeight);
        for(let i = 1; i < 3; i++) {
            ctx.beginPath();
            let moveToY = (startPoint.y + barGap*i) + sinH*2;
            ctx.moveTo(startPoint.x, moveToY);
            addSinPath(drawer, {x: startPoint.x, y: startPoint.y + barGap*i}, sinH, width);
            ctx.lineTo(startPoint.x + width, (startPoint.y + barGap*i + barWidth) + sinH*2);
            let moveToY2 = (startPoint.y + barGap*i + barWidth);
            addSinPath(drawer, {x: startPoint.x + width, y: moveToY2}, sinH, width, true);
            ctx.closePath();
            if(fillShape) ctx.fill();
            else ctx.stroke();
        }
    }

    function addSinPath(drawer, point, sinH, width, reverse = false) {
        let ctx = drawer.context2d;

        let f = 1;
        let offX = point.x,
            offY = point.y,
            startX = reverse ? point.x : offX,
            endX = reverse ? (point.x - width) : (width + offX);
        let y, x;
        for(x = startX; ; x = reverse ? --x : ++x ) {
            if (reverse) {
                if(x < endX) break;
            } else {
                if(x > endX) break;
            }
            y = offY + sinH - sinH * Math.sin( (x-offX) * 2 * Math.PI * (f/width) - Math.PI/2);
            ctx.lineTo(x,y);
        }
        return {x: x, y: y};
    }


    function drawRectWithCrossbars(drawer, rectangles, bars) {
        let ctx = drawer.context2d;
        rectangles.forEach(function(rect, ind) {
            drawRect(drawer, rect);
            if(ind === 0) {
                rect.grow(new Geom.Vect(-ctx.lineWidth/2, -ctx.lineWidth/2));
                fillRect(drawer, rect);
            }
        });
        bars.forEach(function(bar) {
            drawRect(drawer, bar);
        });
        ctx.globalCompositeOperation='destination-out';
        ctx.fillStyle = '#f4f5f5';
        ctx.globalAlpha = 1;
        bars.forEach(function(bar) {
            fillRect(drawer, bar);
        });
    }

    // Circle

    function drawCircle(drawer, circle) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        addCirclePath(drawer, circle);
        stroke(drawer);
    }

    function fillCircle(drawer, circle) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        addCirclePath(drawer, circle);
        ctx.fill();
    }

    function clipCircle(drawer, circle) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        addCirclePath(drawer, circle);
        ctx.clip();
    }

    // Arc
    function drawArc(drawer, arc) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        addArcPath(drawer, arc);
        stroke(drawer);
    }

    // Ellipse
    function drawEllipse(drawer, ellipse) {
        let ctx = drawer.context2d;
        ctx.save();
        ctx.beginPath();
        let center = scaleVect(drawer, ellipse.center),
            radius = scaleDist(drawer, ellipse.radius);
        ctx.translate(center.x, center.y);
        ctx.scale(0.5, 1);
        drawer.context2d.arc(0, 0, radius, 0, Math.PI * 2);
        ctx.restore();
        stroke(drawer);
    }

    // Rounded Rect
    function drawRoundRect(drawer, handle, radius, roundedSides) {
        let ctx = drawer.context2d;
        ctx.beginPath();
        let pos = scaleVect(drawer, handle.pos);
        roundRect(ctx, pos.x, pos.y - radius*0.3, handle.width + radius*4, handle.height + radius, radius, roundedSides);
        ctx.closePath();
        ctx.fill();
        stroke(drawer);
    }

    function roundRect(ctx, x, y, width, height, radius, roundedSides) {
        if (typeof radius === "number") {
            let tl = roundedSides == 'left' ? 0 : radius,
                tr = roundedSides == 'left' ? radius : 0,
                br = roundedSides == 'left' ? radius : 0,
                bl = roundedSides == 'left' ? 0 : radius;
            radius = {
                tl: tl,
                tr: tr,
                br: br,
                bl: bl
            }
        }
        ctx.moveTo(x + radius.tl, y);

        ctx.lineTo(x + width - radius.tr, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
        ctx.lineTo(x + width, y + height - radius.br);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
        ctx.lineTo(x + radius.bl, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
        ctx.lineTo(x, y + radius.tl);
        ctx.quadraticCurveTo(x, y, x + radius.tl, y);
    }

    function roundTriangle(drawer, points, radius) {
        let ctx = drawer.context2d;

        const distance = (p1, p2) => Math.sqrt((p1.x - p2.x) ** 2 + (p1.y - p2.y) ** 2)

        const lerp = (a, b, x) => a + (b - a) * x

        const lerp2D = (p1, p2, t) => ({
            x: lerp(p1.x, p2.x, t),
            y: lerp(p1.y, p2.y, t)
        })
        function range(start, end) {
            var ans = [];
            for (let i = start; i <= end; i++) {
                ans.push(i);
            }
            return ans;
        }

        const numPoints = points.length - 1;

        let corners = []
        for (let i of range(0,numPoints)) {
            let lastPoint = scaleVect(drawer, points[i]);
            let thisPoint = scaleVect(drawer, points[(i + 1) % numPoints]);
            let nextPoint = scaleVect(drawer, points[(i + 2) % numPoints]);

            let lastEdgeLength = distance(lastPoint, thisPoint)
            let lastOffsetDistance = Math.min(lastEdgeLength / 2, radius)
            let start = lerp2D(
                thisPoint,
                lastPoint,
                lastOffsetDistance / lastEdgeLength
            )

            let nextEdgeLength = distance(nextPoint, thisPoint)
            let nextOffsetDistance = Math.min(nextEdgeLength / 2, radius)
            let end = lerp2D(
                thisPoint,
                nextPoint,
                nextOffsetDistance / nextEdgeLength
            )

            corners.push([start, thisPoint, end])
        }

        ctx.moveTo(corners[0][0].x, corners[0][0].y)
        for (let [start, ctrl, end] of corners) {
            ctx.lineTo(start.x, start.y)
            ctx.quadraticCurveTo(ctrl.x, ctrl.y, end.x, end.y)
        }

        ctx.closePath();
        ctx.fill();
        stroke(drawer);
    }


    // Text

    function fillTextOverLine(drawer, segment, text, padding = 4) {
        let ctx = drawer.context2d;
        ctx.save();
        {
            // set font
            let font = drawer.getFont();
            if (font) {
                ctx.font = font;
            }
        }

        let scaledSegment = scaleSegment(drawer, segment),
            length = scaledSegment.length(),
            angle = (new Geom.Vect(1, 0)).angle(scaledSegment.vect()),
            metrics = drawer.context2d.measureText(text),
            offset = (length - metrics.width) / 2;
        let pos = scaledSegment.p1
            .sum(scaledSegment.vect().resized(offset)) // offset from p1
            .sum(scaledSegment.vect().rotated(-Math.PI/2).resized(padding)) // add padding

        ctx.translate(pos.x, pos.y);
        ctx.rotate(-angle);
        drawer.context2d.fillText(text, 0, 0);
        ctx.restore();
    }


    // Image

    function drawImage(drawer, image, pos) {
        // drawRect(drawer, new Geom.Rect(pos, image.width, image.height)); // TEST
        let scaledPos = scaleVect(drawer, pos);
        drawer.context2d.drawImage(
            image,
            scaledPos.x,
            scaledPos.y,
            scaleDist(drawer, image.width),
            scaleDist(drawer, image.height));
    }

    return {
        addToPath: addToPath,
        addPath: addPath,
        addCirclePath: addCirclePath,
        addArcPath: addArcPath,
        drawConnectedPoints: drawConnectedPoints,
        fillClosedPath: fillClosedPath,
        clipClosedPath: clipClosedPath,
        // rect
        drawRect: drawRect,
        fillRect: fillRect,
        shadeRect: shadeRect,
        // line
        drawLine: drawLine,
        // circle
        drawCircle: drawCircle,
        fillCircle: fillCircle,
        clipCircle: clipCircle,
        // arc
        drawArc: drawArc,
        // text
        fillTextOverLine: fillTextOverLine,
        // image
        drawImage: drawImage,
        //curve
        drawCurve: drawCurve,
        drawCurveShape: drawCurveShape,
        //ellipse
        drawEllipse: drawEllipse,
        // rounded rectangle
        drawRoundRect: drawRoundRect,
        roundTriangle: roundTriangle,
        drawSinRectangleShape: drawSinRectangleShape,
        drawRectWithCrossbars: drawRectWithCrossbars
    };
});
