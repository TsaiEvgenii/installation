define([
    '../color',
    '../geometry',
    '../shape',
    '../../measurements/dimension'
], function(Color, Geom, Shape, Dimension) {

    let Type = 'semicircle',
        Name = 'Semicircle',
        Options = {
            base: [
                {name: 'Bottom', value: 'bottom'},
                {name: 'Left', value: 'left'},
                {name: 'Top', value: 'top'},
                {name: 'Right', value: 'right'}
            ],
            param_type: [
                {name: 'Width', value: 'width'},
                {name: 'Radius', value: 'radius'}
            ]
        };

    class Semicircle extends Shape.Base {
        constructor() {
            super(Type);
            this.params.base = 'bottom';
            this.params.param_type = 'width';
            this.params.width = null;
            this.params.width_min = 0;
            this.params.width_max = null;
            this.params.is_customizable = false;
            this.params.min_distance = 15;
            this.params.reduce_R = 5;
        }

        place() {
            // do nothing
        }

        getBorderLine() {
            let block = this.parent,
                pad = block.borderPad();
            return this._getLineAdj(pad);
        }

        getBorderArc() {
            let block = this.parent,
                pad = block.borderPad();
            return this._getArcAdj(pad);
        }

        getPaddingLine() {
            let block = this.parent,
                pad = -block.borderIn();
            return this._getLineAdj(pad);
        }

        getPaddingArc() {
            let block = this.parent,
                pad = -block.borderIn();
            return this._getArcAdj(pad);
        }

        getInnerBorderLine() {
            let block = this.parent,
                pad = -block.padding + block.innerBorderPad();
            return this._getLineAdj(pad);
        }

        getInnerBorderArc() {
            let block = this.parent,
                pad = -block.padding + block.innerBorderPad();
            return this._getArcAdj(pad);
        }

        getFeatureClipLine() {
            let block = this.parent,
                padding = Math.max(block.getFeaturePadding(), block.borderIn()),
                pad = -padding - block.getInnerBorder() / 2;
            return this._getLineAdj(pad);
        }

        getFeatureClipArc() {
            let block = this.parent,
                padding = Math.max(block.getFeaturePadding(), block.borderIn()),
                pad = -padding - block.getInnerBorder() / 2;
            return this._getArcAdj(pad);
        }

        getFeatureBox() {
            let block = this.parent,
                line = this.getFeatureClipLine(),
                height = this.getRadius() - block.padding + block.innerBorderPad(),
                p3 = line.p2.sum(this._outwardVect(-height)),
                diagonal = new Geom.Segment(line.p1.copy(), p3);
            return diagonal.getBoundingRect();
        }

        getParentDimension (dimension) {
            let parentBlock = this.parent.parent || this.parent,
                dimensionValue = 0,
                adjSum = 0;
            while (dimensionValue == 0) {
                if (dimension == 'width') {
                    dimensionValue = parentBlock.width !== null ? parentBlock.width : 0;
                } else {
                    dimensionValue = parentBlock.height !== null ? parentBlock.height : 0;
                }
                parentBlock = parentBlock.parent ? parentBlock.parent : parentBlock;
            }
            if(parentBlock.getMeasurementByDimension(dimension))
                adjSum = parentBlock.getMeasurementByDimension(dimension)._getAdjustmentSum();
            return dimensionValue + adjSum;
        }

        getRadius() {
            let dimension = (this.params.base == 'bottom' || this.params.base == 'top') ? 'width' : 'height',
                parentWidth = this.getParentDimension(dimension),
                distance = (parentWidth - this.getWidth()) / 2,
                R = this.getWidth() / 2;
            if (distance < this.params.min_distance) {
                while (distance < this.params.min_distance) {
                    R -= this.params.reduce_R;
                    distance += parseInt(this.params.reduce_R);
                }
            }
            return R;
        }

        setRadius(radius) {
            this._setWidth(radius === null ? radius : radius * 2)
        }

        getWidth() {
            let box = this.parent.box,
                base = this.params.base;
            if (this.params.width !== null) {
                return this.params.width;
            } else if (base == 'left' || base == 'right') {
                return box.height;
            } else {
                return box.width;
            }
        }

        setWidth(width) {
            this.params.width = width ;
        }

        _getLine() {
            let baseLine = this._getBaseLine(),
                width  = this.getRadius() * 2;
            return baseLine.grown(width - baseLine.length());
        }

        _getArc() {
            let center = this._getBaseLine().center(),
                radius = this.getRadius(),
                angles = this._getArcAngles();
            return new Geom.Arc(center, radius, ...angles);
        }

        _getArcAngles() {
            let pi = Math.PI;
            switch (this.params.base) {
            case 'bottom':
                return [pi, 0];
            case 'left':
                return [pi / 2 * 3, pi / 2];
            case 'top':
                return [0, pi];
            case 'right':
                return [pi / 2, pi / 2 * 3];
            }
        }

        _getBaseLine() {
            let box = this.parent.box,
                base = this.params.base;
            switch (base) {
            case 'bottom':
                return box.bottom();
            case 'left':
                return box.left();
            case 'top':
                return box.top();
            case 'right':
                return box.right();
            }
            throw "Invalid base `" + base + "'";
        }

        _outwardVect(distance) {
            let base = this.params.base;
            switch (base) {
            case 'bottom':
                return new Geom.Vect(0, distance);
            case 'left':
                return new Geom.Vect(-distance, 0);
            case 'top':
                return new Geom.Vect(0, -distance);
            case 'right':
                return new Geom.Vect(distance, 0);
            }
            throw "Invalid base `" + base + "'";
        }

        _getLineAdj(pad) {
            let radius = this.getRadius() + pad;
            return this._getLine()
                .moved(this._outwardVect(pad))
                .grown(this._getLineAdjValue(radius, pad) * 2);
        }

        _getArcAdj(pad) {
            let arc = this._getArc().grown(pad),
                lineAdj = this._getLineAdjValue(arc.radius, pad),
                angleAdj = Math.atan2(-pad, this.getRadius() + lineAdj);
            arc.startAngle += angleAdj;
            arc.endAngle -= angleAdj;
            return arc;
        }

        _getLineAdjValue(radius, pad) {
            return pad - radius + Math.sqrt(radius * radius - pad * pad);
        }
    }

    return {
        Type: Type,
        Name: Name,
        Options: Options,
        Shape: Semicircle
    };
});
