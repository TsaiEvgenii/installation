define(function() {

    // TODO: refactoring: uniform interface (e.g. vertices / vertices())

    function coordToString(value) {
        return value !== null
            ? value.toString()
            : 'nil';
    }

    function numberOrNull(value) {
        if (value === null) {
            return null;
        } else {
            let num = Number(value);
            return isNaN(num)
                ? null
                : num;
        }
    }

    class Vect {
        constructor(x, y) {
            this._x = numberOrNull(x);
            this._y = numberOrNull(y);
        }

        copy() {
            return new Vect(this._x, this._y);
        }

        equal(other) {
            // NOTE: null == 0
            return (this._x == other.x) && (this._y == other.y);
        }

        isnull() {
            return this._x == 0.0 && this._y == 0.0;
        }

        normalize() {
            if (this.isnull()) {
                throw "Cannot normalize null vector";
            }
            let magnitude = this.magnitude();
            this._x /= magnitude;
            this._y /= magnitude;
        }

        add(other) {
            this._x += other.x;
            this._y += other.y;
        }

        sub(other) {
            this._x -= other.x;
            this._y -= other.y;
        }

        mult(factor) {
            this._x *= factor;
            this._y *= factor;
        }

        inverse() {
            this.mult(-1);
        }

        rotate(angle) {
            let x = this._x * Math.cos(angle) - this._y * Math.sin(angle),
                y = this._x * Math.sin(angle) + this._y * Math.cos(angle);
            this._x = x;
            this._y = y;
        }

        resize(magnitude) {
            this.normalize();
            this.mult(magnitude);
        }

        normalized() {
            let result = this.copy();
            result.normalize();
            return result;
        }

        sum(other) {
            return new Vect(this._x + other.x, this._y + other.y);
        }

        diff(other) {
            return new Vect(this._x - other.x, this._y - other.y);
        }

        product(factor) {
            return new Vect(this._x * factor, this._y * factor);
        }

        inversed() {
            return this.product(-1);
        }

        rotated(angle) {
            let result = this.copy();
            result.rotate(angle);
            return result;
        }

        resized(magnitude) {
            return this.normalized().product(magnitude);
        }

        between(other, fraction = 0.5) {
            return this.sum(other.diff(this).product(fraction));
        }

        dot(other) {
            return this._x * other.x + this._y * other.y;
        }

        magnitude() {
            let x = this._x,
                y = this._y;
            return Math.sqrt(x * x + y * y);
        }

        angle(other) {
            return Math.acos(this.dot(other) / (this.magnitude() * other.magnitude()));
            // return Math.atan2(this._x, this._y) - Math.atan2(other.x, other.y);
        }

        distance(other) {
            return other.diff(this).magnitude();
        }

        closest(points) {
            let idx = this.closestIndex(points);
            return (idx != -1)
                ? points[idx]
                : null;
        }

        closestIndex(points) {
            let min = null,
                result = -1;
            for (let idx in points) {
                let point = points[i],
                    distance = this.distance(point);
                if (min === null || distance < min) {
                    min = distance;
                    result = idx;
                }
            }
            return idx;
        }

        toString() {
            return '(' + coordToString(this._x) + ' '
                + coordToString(this._y) + ')'
        }

        get x() { return this._x; }
        set x(x) { this._x = numberOrNull(x); }

        get y() { return this._y; }
        set y(y) { this._y = numberOrNull(y); }
    }

    class Circle {
        constructor(center, radius) {
            this._center = center;
            this._radius = radius;
        }

        copy() {
            return new Circle(this._center.copy(), this._radius);
        }

        move(vect) {
            this._center.add(vect);
        }

        moved(vect) {
            let result = this.copy();
            result.move(vect);
            return result;
        }

        grow(value) {
            this._radius += value;
        }

        grown(value) {
            let result = this.copy();
            result.grow(value);
            return result;
        }

        getBoundingRect() {
            let offset = new Vect(-this._radius, -this._radius),
                side = this._radius * 2;
            return new Rect(this._center.sum(offset), side, side);
        }

        toString() {
            return '(' + this._center.toString() + ' '
                + coordToString(this._radius) + ')';
        }

        get center() { return this._center; }

        get radius() { return this._radius; }
        set radius(radius) { this._radius = radius; }
    }

    class Arc {
        constructor(center, radius, startAngle = 0, endAngle = Math.PI * 2) {
            this._center = center;
            this._radius = radius;
            this._startAngle = startAngle;
            this._endAngle = endAngle;
        }

        copy() {
            return new Arc(
                this._center.copy(), this._radius,
                this._startAngle, this._endAngle);
        }

        move(vect) {
            this._center.add(vect);
        }

        moved(vect) {
            let result = this.copy();
            result.move(vect);
            return result;
        }

        grow(value) {
            this._radius += value;
        }

        grown(value) {
            let result = this.copy();
            result.grow(value);
            return result;
        }

        getBoundingRect() { return false; /* TODO */}

        toString() {
            return '(' + this._center.toString() + ' '
                + coordToString(this._radius) + ' '
                + coordToString(this._startAngle) + ' '
                + coordToString(this._endAngle) + ')';
        }

        get center() { return this._center; }

        get radius() { return this._radius; }
        set radius(radius) { this._radius = radius; }

        get startAngle() { return this._startAngle; }
        set startAngle(startAngle) { this._startAngle = startAngle; }

        get endAngle() { return this._endAngle; }
        set endAngle(endAngle) { this._endAngle = endAngle; }
    }

    class Segment {
        constructor(p1, p2) {
            this._p1 = p1 || new Vect();
            this._p2 = p2 || new Vect();
        }

        copy() {
            return new Segment(this._p1.copy(), this._p2.copy());
        }

        vect() {
            return this._p2.diff(this._p1);
        }

        length() {
            return this._p1.diff(this._p2).magnitude();
        }

        move(vect) {
            this._p1.add(vect);
            this._p2.add(vect);
        }

        moved(vect) {
            let result = this.copy();
            result.move(vect);
            return result;
        }

        grow(value) {
            let length = this.length();
            if (length == 0)
                throw "Cannot grow zero lenght segment";
            if (value == 0) return;

            let adj = this._p2.diff(this._p1).resized(value / 2);
            this._p1.sub(adj);
            this._p2.add(adj);
        }

        grown(value) {
            let result = this.copy();
            result.grow(value);
            return result;
        }

        center() {
            return this._p1.between(this._p2);
        }

        vertices() {
            return [this._p1, this._p2];
        }

        getBoundingRect() {
            let pos = new Vect(
                Math.min(this._p1.x, this._p2.x),
                Math.min(this._p1.y, this._p2.y));
            return new Rect(
                pos,
                Math.abs(this._p2.x - this._p1.x),
                Math.abs(this._p2.y - this._p1.y));
        }

        toString() {
            return '(' + this._p1.toString()
                + ' ' + this._p2.toString() + ')'
        }

        get p1() { return this._p1; }
        get p2() { return this._p2; }
    }

    class Rect {
        constructor(pos, width, height) {
            this._pos = pos ? pos.copy() : new Vect();
            this._width = numberOrNull(width);
            this._height = numberOrNull(height);
        }

        copy() {
            return new Rect(this._pos, this._width, this._height);
        }

        move(vect) {
            this._pos.add(vect);
        }

        grow(vect) {
            this._width += (vect.x * 2);
            this._height += (vect.y * 2);
            this.move(vect.inversed());
        }

        moved(vect) {
            let result = this.copy();
            result.move(vect);
            return result;
        }

        grown(vect) {
            let result = this.copy();
            result.grow(vect);
            return result;
        }

        combine(other) {
            let x = Math.min(this._pos.x, other.pos.x),
                width = Math.max(this._pos.x + this._width, other.pos.x + other.width) - x,
                y = Math.min(this._pos.y, other.pos.y),
                height = Math.max(this._pos.y + this._height, other.pos.y + other.height) - y;
            this._pos.x = x;
            this._pos.y = y;
            this.width = width;
            this.height = height;
        }

        combined(other) {
            let result = this.copy();
            result.combine(other);
            return result;
        }

        center() { return this.topLeft().between(this.bottomRight()); }

        topLeft() { return this._pos.copy(); }
        topRight() { return this.topLeft().sum(new Vect(this._width, 0)); }
        bottomLeft() { return this.topLeft().sum(new Vect(0, this._height)); }
        bottomRight() { return this.bottomLeft().sum(new Vect(this._width, 0)); }

        top() { return new Segment(this.topLeft(), this.topRight()); }
        bottom() { return new Segment(this.bottomRight(), this.bottomLeft()); }
        left() { return new Segment(this.bottomLeft(), this.topLeft()); }
        right() { return new Segment(this.topRight(), this.bottomRight()); }

        vertices() {
            // clockwise
            return [
                this.topLeft(),
                this.topRight(),
                this.bottomRight(),
                this.bottomLeft()
            ];
        }

        sides() {
            return [this.left(), this.top(), this.right(), this.bottom()];
        }

        toString() {
            return '(' + this.pos.toString()
                + ' ' + coordToString(this._width)
                + ' ' + coordToString(this._height) + ')';
        }

        get pos() { return this._pos; }
        set pos(pos) { this._pos = pos; }

        get width() { return this._width; }
        set width(width) { this._width = numberOrNull(width); }

        get height() { return this._height; }
        set height(height) { this._height = numberOrNull(height); }
    }

    class Polygon {
        constructor(vertices) {
            this._vertices = vertices || [];
        }

        copy() {
            let vertices = this._vertices.map(function(vertex) {
                return vertex.copy();
            });
            return new Polygon(vertices);
        }

        valid() {
            if (this._vertices.length < 3) {
                return false;
            }
            let num = this._vertices.length;
            for (let i = 0; i < num; ++i) {
                let p0 = this._vertices[i],
                    v1 = this._vertices[(i - 1 + num) % num].diff(p0),
                    v2 = this._vertices[(i + 1 + num) % num].diff(p0);
                if (v1.isnull() || v2.isnull()) {
                    return false;
                }
            }
            return true;
        }

        grow(dist) {
            let newVertices = [],
                num = this._vertices.length;
            for (let i = 0; i < num; ++i) {
                let p0 = this._vertices[i],
                    v1 = this._vertices[(i - 1 + num) % num].diff(p0),
                    v2 = this._vertices[(i + 1 + num) % num].diff(p0),
                    angle = v1.angle(v2),
                    magnitude = dist / Math.sin(angle / 2);
                let vect = v1.normalized()
                    .sum(v2.normalized())
                    .inversed()
                    .resized(magnitude);
                newVertices.push(p0.sum(vect));
            }
            this._vertices = newVertices;
        }

        grown(dist) {
            let result = this.copy();
            result.grow(dist);
            return result;
        }

        edges() {
            return pointsToSegmentsClosed(this._vertices);
        }

        vertices() {
            return this._vertices;
        }

        getBoundingRect() {
            let vertices = this._vertices;
            if (vertices.length < 1) {
                throw "Cannot bound empty polygon";
            }

            let minMax = pointsMinMax(vertices);
            return new Rect(
                new Vect(minMax.minX, minMax.minY),
                minMax.maxX - minMax.minX,
                minMax.maxY - minMax.minY);
        }
    }

    function pointsToSegments(points) {
        let segments = [];
        for (let i = 0; i < points.length - 1; ++i) {
            segments.push(new Segment(points[i].copy(), points[i + 1].copy()));
        }
        return segments;
    }

    function pointsToSegmentsClosed(points) {
        return points.length > 1
            ? pointsToSegments(points.concat([points[0]]))
            : [];
    }

    function movesToPoints(start, moves) {
        let point = start.copy(),
            points = [point.copy()];
        moves.forEach(function(move) {
            point.add(move);
            points.push(point.copy());
        });
        return points;
    }

    function pointsMinMax(points) {
        function getFold(compare, field) {
            return function(acc, point) {
                let value = point[field];
                return (acc !== null)
                    ? compare(acc, value)
                    : value;
            }
        }

        function fold(compare, field) {
            return points.reduce(getFold(compare, field), null);
        }

        return {
            minX: fold(Math.min, 'x'),
            minY: fold(Math.min, 'y'),
            maxX: fold(Math.max, 'x'),
            maxY: fold(Math.max, 'y')
        };
    }

    return {
        Vect: Vect,
        Circle: Circle,
        Arc: Arc,
        Segment: Segment,
        Rect: Rect,
        Polygon: Polygon,
        pointsToSegments: pointsToSegments,
        pointsToSegmentsClosed: pointsToSegmentsClosed,
        movesToPoints: movesToPoints,
        pointsMinMax: pointsMinMax
    };
});
