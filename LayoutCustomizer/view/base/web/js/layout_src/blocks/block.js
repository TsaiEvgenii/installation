define([
    './color',
    './drawable',
    './geometry',
    './feature',
    './measurement',
    './shapes/all',
    '../measurements/measurement-dependency-helper'
], function(Color, Drawable, Geom, Feature, Measurement, ShapeList, MeasurementDependencyHelper) {
    let Type = 'block',
        Layout = {
            Horizontal: 'horizontal',
            Vertical: 'vertical'
        },
        BorderPlacement = {
            Inside: 'inside',
            Outside: 'outside',
            Middle: 'middle'
        },
        Params = {
            "pos.x": {label: 'X', type: 'number', nullable: true, order: 10},
            "pox.y": {label: 'Y', type: 'number', nullable: true, order: 20},
            width: {label: 'Width', type: 'number', nullable: true, order: 30},
            height: {label: 'Height', type: 'number', nullable: true, order: 40},
            border: {label: 'Border', type: 'number', order: 50},
            borderPlacement: {label: 'Border Placement', type: 'select', options: [
                {name: "Inside", value: BorderPlacement.Inside},
                {name: "Middle", value: BorderPlacement.Middle},
                {name: "Outside", value: BorderPlacement.Outside}
            ], order: 60},
            borderColor: {label: 'Border Color', type: 'color', order: 70},
            innerBorder: {label: 'InnerBorder', type: 'number', nullable: true, order: 80},
            innerBorderPlacement: {label: 'InnerBorder Placement', type: 'select', options: [
                {name: "Inside", value: BorderPlacement.Inside},
                {name: "Middle", value: BorderPlacement.Middle},
                {name: "Outside", value: BorderPlacement.Outside}
            ], order: 90},
            innerBorderColor: {label: 'InnerBorder Color', type: 'color', order: 100},
            color: {label: 'Color', type: 'color', order: 110},
            padding: {label: 'Padding', type: 'number', nullable: true, order: 120},
            featurePadding: {label: 'Feature Padding', type: 'number', order: 130},
            spacing: {label: 'Spacing', type: 'number', order: 140},
            layout: {label: 'Layout', type: 'select', options: [
                {name: 'Horizontal', value: Layout.Horizontal},
                {name: 'Vertical', value:  Layout.Vertical}
            ], order: 150},
            reverse: {label: 'Reverse children', type: "select", options: [
                {name: 'Normal', value: 'normal'},
                {name: 'Reverse', value: 'reverse'}
            ], order: 160}
        },

        MeasurementDependency = {};
        MeasurementDependency.Type = 'measurement-dependency';

    function throwInvalidDrawableType(type) {
        throw "Invalid drawable type `" + type + "'";
    }

    class Block extends Drawable.Base {
        constructor(pos, width, height) {
            super(Type);
            this._pos = pos ? pos.copy() : new Geom.Vect();
            this._width = width || null;
            this._height = height || null;
            this._border = 1;
            this._borderPlacement = 'middle';
            this._borderColor = '#000000';
            this._innerBorder = 0;
            this._innerBorderPlacement = 'middle';
            this._innerBorderColor = null;
            this._padding = 0;
            this._featurePadding = null;
            this._spacing = 0;
            this._layout = Layout.Horizontal;
            this._box = new Geom.Rect();
            this._children = [];
            this._features = [];
            this._measurements = [];
            this._color = '#FFFFFF';
            this.shape = new ShapeList.rectangle.Shape();
            this._reverse = 'normal';
            this._isReversed = false;
        }

        destroy() {
            this._children.forEach(function(child) {
                child.destroy();
            });
            this._features.forEach(function(feature) {
                feature.destroy();
            });
            this._measurements.forEach(function(measurement) {
                measurement.destroy();
            });

            super.destroy();
        }

        innerBox() {
            // let pad = this._padding + this.borderIn();
            let pad = this._padding;
            return this.box.grown(new Geom.Vect(pad, pad).inversed());
        }

        outerBox() {
            let pad = this.borderOut();
            return this.box.grown(new Geom.Vect(pad, pad));
        }

        isPositioned(coord) {
            return coord
                ? this.pos[coord] != null
                : (this.pos.x !== null) && (this.pos.y !== null);
        }

        add(child) {
            child.parent = this;
            this._getChildList(child.drawableType).push(child);
        }

        insert(child, position) {
            child.parent = this;
            let list = this._getChildList(child.drawableType);
            list.splice(position, 0, child);
        }

        remove(child) {
            let list = this._getChildList(child.drawableType),
                idx = list.indexOf(child);
            if (idx != -1) {
                list.splice(idx, 1);
                child.parent = null;
            }
        }

        getPosition(child) {
            return this._getChildList(child.drawableType).indexOf(child);
        }

        _getChildList(type) {
            switch (type) {
            case Type:
                return this._children;
            case Feature.Type:
                return this._features;
            case Measurement.Type:
                return this._measurements;
            default:
                throwInvalidDrawableType(child.drawableType);
            }
        }

        siblings() {
            return this.parent
                ? this.parent.children.filter(child => this != child)
                : [];
        }

        reset() {
            this._box = new Geom.Rect();
            this._children.forEach(function(child) { child.reset(); });
        }

        prepare() {
            this._children.forEach(function(child) { child.prepare(); });
            this.prepareBox();
        }

        place() {
            this.placeChildren();
            this._shape.prepare();
            this._shape.place();
            this._children.forEach(function(child) { child.place(); });
            this._features.forEach(function(feature) { feature.place(); });
            this._measurements.forEach(function(measurement) { measurement.place(); });
        }

        prepareBox() {
            if (this._box.pos.x === null) {
                this._box.pos.x = this.pos.x;
                if (this._box.pos.x === null && this.isRoot())
                    this._box.pos.x = 0;
            }
            if (this._box.pos.y === null) {
                this._box.pos.y = this.pos.y;
                if (this._box.pos.y === null && this.isRoot())
                    this._box.pos.y = 0;
            }
            if (this._box.width === null) {
                this._box.width  = (this.width !== null) ? this.width : this.calcWidth();
            }
            if (this._box.height === null) {
                this._box.height = (this.height !== null) ? this.height : this.calcHeight();
            }
        }

        calcWidth() {
            return this.calcDimension('width', 'x');
        }

        calcHeight() {
            return this.calcDimension('height', 'y');
        }

        calcDimension(dimension, coord) {
            // Return dimension if set for block
            if (this[dimension]) {
                return this[dimension];
            }

            // Get non-positioned children
            var childrenNonPos = this._children.filter(function(child) {
                return !child.isPositioned(coord);
            });

            // Cannot determine value if there are no children
            if (childrenNonPos.length == 0) {
                return null;
            }

            // Extract child dimension values
            var childValues = childrenNonPos.map(function(child) {
                return (child.box[dimension] === null)
                    ? null
                    : child.box[dimension] + 2 * child.borderOut();
            }).filter(value => value !== null); // only non-null

            // Calc. using children's dimension values
            var pad = 2 * this._padding + 2 * this.borderIn();
            if ((dimension == 'width') == (this.layout == Layout.Horizontal)) {
                // Check if every child has dimension value set
                if (childValues.length != childrenNonPos.length) {
                    return null;
                }
                // sum + spacing + padding + border
                return childValues.reduce((a, b) => a + b, 0)
                    + (childValues.length - 1) * this._spacing + pad;
            } else {
                // max + padding + border
                return childValues.reduce((a, b) => Math.max(a, b), 0) + pad;
            }
        }

        getValueByDimension (dimension) {
            if(dimension == 'height') {
                return this.height;
            } else {
                if (dimension == 'width') {
                    return this.width;
                }
            }
        }
        //value in input or block value by dimension + adjustmentSum()
        getFullValueByDimension(dimension) {
            let measurementByDimension = this.getMeasurementByDimension(dimension),
                fullValue = this.getValueByDimension(dimension);
            if(measurementByDimension) {
                fullValue = measurementByDimension.getMeasurement().input.getValue();
            }
            return fullValue;
        }

        setValueByDimension (dimension, value) {
            if(dimension == 'height') {
                this.height = value;
            } else {
                if (dimension == 'width') {
                    this.width = value;
                }
            }
        }

        getChildIndexByObjectId (objectId) {
            return this.children.findIndex(function(child) {
                if(child.objectId.isSame(objectId)) {
                    return true;
                }
            })
        }

        getCustomizableChildren (dimension) {
            return this.children.filter(child => child.getMeasurementByDimension(dimension).isCustomizable);
        }

        hasFeatureWithValue(featureType, value) {
            return this.features.find(function (feature) {
                return feature.featureType === featureType && feature.params['type'] === value;
            })
        }

        hasSlidingDoors() {
            return this.children.find(function (child) {
                return child.hasFeatureWithValue('sliding-door', 'sliding');
            })
        }

        getChildWithFeature(featureType, value) {
            return this.children.find(function(child) {
                return child.hasFeatureWithValue(featureType, value);
            })
        }

        placeChildren() {
            var isHorizontal = (this._layout == Layout.Horizontal),
                width_  = isHorizontal ? 'width' : 'height',
                height_ = isHorizontal ? 'height' : 'width',
                x_ = isHorizontal ? 'x' : 'y',
                y_ = isHorizontal ? 'y' : 'x',
                innerBox = this.innerBox();

            // Set height to parent's inner box height if not set
            this._children.forEach(function(child) {
                if (child[height_] === null) {
                    // child.box[height_] = innerBox[height_] - 2 * child.borderOut();
                    child.box[height_] = innerBox[height_];
                }
            }, this);

            // Collect non-positioned children
            var childrenNonPos = this._children.filter(function(child) {
                return !child.isPositioned(x_);
            });
            {
                // Collect children with empty width
                let blocksNoWidth = childrenNonPos.filter(function(child) {
                    return child[width_] === null;
                });

                if (blocksNoWidth.length > 0) {
                    // Calc. free width space
                    let spaceLeft = innerBox[width_]
                        - (this._spacing * (childrenNonPos.length - 1));
                    childrenNonPos.forEach(function(child) {
                        // let childFullWidth = (child[width_] !== null)
                        //     ? child[width_] + 2 * child.borderOut()
                        //     : 0;
                        let childFullWidth = (child[width_] !== null)
                            ? child[width_]
                            : 0;
                        spaceLeft -= childFullWidth;
                    });
                    // Calc. and set default width
                    let width = (spaceLeft / blocksNoWidth.length);
                    blocksNoWidth.forEach(function(child) {
                        // child.box[width_] = width - 2 * child.borderOut();
                        child.box[width_] = width;
                        if(child.getMeasurementByDimension(width_) &&
                            !child.getMeasurementByDimension(width_).isCustomizable &&
                            child.getMeasurementByDimension(width_)._measurement)
                            if(child.getMeasurementByDimension(width_)._measurement._input) {
                                child.getMeasurementByDimension(width_).setCheckMeasurementValue();

                                // Calculate child height if door has only sashes with NO CUSTOMIZABLE input
                                if(child.parent && child.parent.parent === null && width_ === 'height' && child.parent.getFeatureByType('half-door')) {
                                    let parentBlockHeight = spaceLeft +  child.parent.getFeatureByType('door-frame').params.width;
                                    let measurement = child.getMeasurementByDimension(width_);
                                    let newHeight = parentBlockHeight / blocksNoWidth.length - measurement._getAdjustmentSum();
                                    child.box[width_] = newHeight;
                                }

                                //ADD dependencyCheck HERE
                                MeasurementDependencyHelper.checkMeasurementDependency(
                                    child,
                                    child.getMeasurementByDimension(width_)._measurement._input._context.objectManager,
                                    child.getMeasurementByDimension(width_)._measurement.getValue(),
                                    width_,
                                    child.getMeasurementByDimension(width_)._measurement._input
                                );
                            }
                    });
                }
            }

            // Set positions
            {
                let pos = innerBox.pos.copy();
                if(this.reverse == 'normal' || this.reverse == '') {
                    if (this._isReversed) {
                        this._children.reverse();
                        this._isReversed = false;
                    }
                } else if(this.reverse == 'reverse') {
                    if(!this._isReversed) {
                        this._children.reverse();
                        this._isReversed = true;
                    }
                }
                this._children.forEach(function(child) {
                    // Set position
                    ['x', 'y'].forEach(function(coord) {
                        if (child.box.pos[coord] === null) {
                            child.box.pos[coord] = pos[coord];
                        }  else {
                            child.box.pos[coord] += innerBox.pos[coord];
                        }
                        // child.box.pos[coord] += child.borderOut();
                    });
                    // Move forward
                    if (!child.isPositioned(x_)) {
                        // pos[x_] += child.box[width_] + this._spacing + child.borderOut();
                        pos[x_] += child.box[width_] + this._spacing;
                    }
                }, this);
            }
        }

        calcNoneMeasurementBlockValue (dimension) {
            if (this.getValueByDimension(dimension) == null && this.getMeasurementByDimension(dimension)._measurement) {
                let parent = this.parent;
                if(parent.getValueByDimension(dimension) && parent.getMeasurementByDimension(dimension)) {
                    // let dimensionValue = parent.getValueByDimension(dimension) - parent.getMeasurementByDimension(dimension)._getAdjustmentSum() - parent._padding * 2,
                    let dimensionValue = parent.getValueByDimension(dimension),
                        blocksNoWidth = parent.children.filter(function (child) {
                            return child[dimension] === null;
                        });

                    if (blocksNoWidth.length > 0) {
                        // Calc. free width space
                        let spaceLeft = dimensionValue
                            - (parent._spacing * (parent.children.length - 1));
                        parent.children.forEach(function (child) {
                            let childFullWidth = (child[dimension] !== null)
                                ? child[dimension]
                                : 0;
                            spaceLeft -= childFullWidth;
                        });
                        // Calc. and set default width
                        return (spaceLeft / blocksNoWidth.length);
                    }
                }
            }
        }

        getMeasurementByDimension (dimension) {
            return this.measurements.find(function (measurement) {
                if(measurement.getField() === dimension) return true;
            });
        }

        hasMeasurementDependency() {
            let result = false;
            this.features.forEach(feature => {
                if(feature.featureType === MeasurementDependency.Type) {
                    result = true;
                    return false;
                }
            });
            return result;
        }

        getMeasurementDependencyBreakPoint() {
            let bp = null;
            this.features.forEach(feature => {
                if(feature.featureType === MeasurementDependency.Type) {
                    bp = feature.breakPoint;
                    return false;
                }
            });
            return bp;
        }

        getAllLinkedObjects(linkedObjects, objectID, dimension){
            this.children.forEach(function(child) {
                if(child.objectData.links.length > 0){
                    child.objectData.links.forEach(function(link) {
                        if(link._name === dimension && link._ref.isSame(objectID)) {
                            linkedObjects.push(child);
                        }
                    })
                }
                child.getAllLinkedObjects(linkedObjects, objectID, dimension);
            });
        }

        getBlocksMeasurementByDimension(dimension, om) {
            let measurement = this.getMeasurementByDimension(dimension);
            if(measurement === undefined) {
                if(this.objectData.links.length > 0) {
                    this.objectData.links.forEach(function (refObj) {
                        if (refObj['_name'] === dimension) {
                            let refObject = om.get(refObj._ref);
                            if (refObject.getMeasurementByDimension(dimension)) {
                                measurement = refObject.getMeasurementByDimension(dimension);
                                return false;
                            }
                        }
                    });
                }
                if(measurement === undefined && this.parent) {
                    measurement = this.parent.getBlocksMeasurementByDimension(dimension, om);
                }
            }
            return measurement;
        }

        getNonMeasurementChildrenByDimension(dimension) {
            return this.children.filter(child => child.getValueByDimension(dimension) === null);
        }


        getFeaturePadding() {
            return (this._featurePadding === null)
                ? this._padding : this._featurePadding;
        }

        getFeatureByType(typeName) {
            return this.features.find((feature) => feature.featureType === typeName);
        }

        getInnerBorder() {
            return (this._innerBorder !== null)
                ? this._innerBorder
                : this._border;
        }

        borderIn() {
            return this._border / 2 - this.borderPad();
        }

        borderOut() {
            return this._border - this.borderIn();
        }

        borderPad() {
            return this._borderPad(this._border, this._borderPlacement);
        }

        innerBorderIn() {
            return this.getInnerBorder() / 2 - this.innerBorderPad();
        }

        innerBorderOut() {
            return this.getInnerBorder() - this.innerBorderIn();
        }

        innerBorderPad() {
            return this._borderPad(this.getInnerBorder(), this._innerBorderPlacement);
        }

        // For how much border center line is moved
        // outside due to placement
        _borderPad(border, placement) {
            switch (placement) {
            case BorderPlacement.Inside:
                return -border / 2;
            case BorderPlacement.Outside:
                return border / 2;
            case BorderPlacement.Middle:
                return 0;
            default:
                throw "Invalid block border placement `" + placement + "'";
            }
        }

        getBoundingRect() {
            function getRect(object) {
                return object.getBoundingRect();
            }
            function notEmpty(value) {
                return !!value;
            }
            function combine(acc, value) {
                return acc.combined(value);
            }

            let rect = this.outerBox();
            rect = this.shape.getMeasurements().map(getRect)
                .filter(notEmpty)
                .reduce(combine, rect);
            rect = this.features.map(getRect)
                .filter(notEmpty)
                .reduce(combine, rect);
            rect = this.measurements.map(getRect)
                .filter(notEmpty)
                .reduce(combine, rect);
            rect = this.children.map(getRect)
                .filter(notEmpty)
                .reduce(combine, rect);
            return rect;
        }

        get box() { return this._box; }

        get pos() { return this._pos; }
        set pos(pos) { this._pos = pos; }

        get width() { return this._width; }
        set width(width) { this._width = width; }

        get height() { return this._height; }
        set height(height) { this._height = height; }

        get layout() { return this._layout; }
        set layout(layout) { this._layout = layout; }

        get reverse() { return this._reverse; }
        set reverse(reverse) { this._reverse = reverse; }

        get border() { return this._border; }
        set border(border) { this._border = border; }

        get borderPlacement() { return this._borderPlacement; }
        set borderPlacement(borderPlacement) { this._borderPlacement = borderPlacement; }

        get borderColor() { return this._borderColor; }
        set borderColor(borderColor) { this._borderColor = borderColor; }

        get innerBorder() { return this._innerBorder; }
        set innerBorder(innerBorder) { this._innerBorder = innerBorder; }

        get innerBorderPlacement() { return this._innerBorderPlacement; }
        set innerBorderPlacement(innerBorderPlacement) {
            this._innerBorderPlacement = innerBorderPlacement; }

        get innerBorderColor() { return this._innerBorderColor; }
        set innerBorderColor(innerBorderColor) {
            this._innerBorderColor = innerBorderColor; }

        get padding() { return this._padding; }
        set padding(padding) { this._padding = padding; }

        get featurePadding() { return this._featurePadding; }
        set featurePadding(featurePadding) { this._featurePadding = featurePadding; }

        get spacing() { return this._spacing; }
        set spacing(spacing) { this._spacing = spacing; }

        get color() { return this._color; }
        set color(color) { this._color = color; }

        get shape() { return this._shape; }
        set shape(shape) {
            shape.parent = this;
            this._shape = shape;
        }

        get children() { return this._children; }
        get features() { return this._features; }
        get measurements() { return this._measurements; }
    }

    return {
        Type: Type,
        Layout: Layout,
        BorderPlacement: BorderPlacement,
        Params: Params,
        Block: Block
    };
});
