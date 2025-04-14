define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'door-shape',
        Name = 'Door Shape',
        Params = {
            type: {label: 'Type', type: 'select', options: [
                {name: 'None', value: 'none'},
                {name: 'Semicircle', value: 'semicircle'},
                {name: 'Curved', value: 'curved'},
                {name: 'Sinusoid rectangle', value: 'sin-rectangle'},
                {name: 'Rectangle', value: 'rectangle'},
                {name: 'Middle Rectangle', value: 'middle-rectangle'},
                {name: 'Squares', value: 'squares'},
                {name: 'Rectangle with crossbars', value: 'rectangle-crossbars'},
                {name: 'Crosslines', value: 'crosslines'}
            ]},
            side: { label: "Side", type: "select", options: [
                {name: "Left", value: "left"},
                {name: "Right", value: "right"}
            ]},
            squaresNumber: {label: "Number of squares", type: 'number'},
            squareWidth: {label: "Squares Width", type: 'number'},
            squareHeight: { label: "Squares Height", type: "select", options: [
                    {name: "As Width", value: "as-width"},
                    {name: "Auto", value: "auto"}
                ]},
            squareDistance: {label: "Distance between squares", type: 'number'},
            squaresSide: { label: "Squares Side", type: "select", options: [
                    {name: "Left", value: "left"},
                    {name: "Right", value: "right"},
                    {name: "Center", value: "center"}
                ]},
            lineColor: {label: 'Line Color', type: 'color'}
        };

    class DoorShape extends Feature.Base {
        constructor() {
            super(Type);
            this.params.type = 'semicircle';
            this.params.side = 'left';
            this._lines = [];
            this._bezierLines = [];

            this._startPoint = '';
            this._endPoint = '';
            this._offsetX = 0;
            this._offsetY = 0;
            this._lineLength = 0;

            this.params.lineWidth = null;
            this.params.color = '#c1e7f8';
            this.params.opacity = 0.7;

            this.params.squaresNumber = 3;
            this.params.squareWidth = null;
            this.params.squareHeight = "as-width";
            this.params.squareDistance = 10;
            this.params.squaresSide = 'center';
            this.params.min_dist = 15;
            this.params.reduce_value = 20;
        }

        place() {
            this._getLines();
        }

        _getLines() {
            switch (this.params.type) {
                case 'none':
                    return [];
                case 'semicircle':
                    return this._getLinesSemicircle(this.params.side);
                case 'curved':
                    return this._getLinesCurved(this.params.side);
                case 'sin-rectangle':
                    return this._getLinesSinRectangle();
                case 'rectangle':
                    return this._getLinesRectangle(this.params.side);
                case 'squares':
                    return this._getSquares(this.params.squaresSide);
                case 'middle-rectangle':
                    return this._getLinesMiddleRectangle();
                case 'rectangle-crossbars':
                    return this._getRectangleAndCrossbars();
                case 'crosslines':
                    return this._getCrosslines(this.params.side);
                default:
                    throw "Invalid type: `" + this.params.type + "'";
            }
        }

        getLineWidth() {
            return this.params.lineWidth !== null
                ? this.params.lineWidth
                : this.parent.border;
        }

        getLineColor() {
            return this.params.lineColor !== null
                ? this.params.lineColor
                : this.parent.borderColor;
        }

        _getBox() {
            let halfLineWidth = this.getLineWidth() / 2;
            return this.parent.shape.getFeatureBox()
                .grown(new Geom.Vect(-halfLineWidth, -halfLineWidth));
        }

        _getRectangleAndCrossbars() {
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height'),
                parentWidth = this.getParentDimension('width'),
                rectangleWidth = (parentWidth > 79) ? 60 : 40,
                padTop = parentHeight*0.1,
                padSide = (rect.width - rectangleWidth) / 2 + this.getLineWidth();
            let height = parentHeight - 2*padTop,
                topRectHeight = height * 0.7,
                bottomRectHeight = height * 0.2,
                spaceBetweenRects = height - topRectHeight - bottomRectHeight;


            this._startPoint = new Geom.Vect(padSide, padTop);
            let bottomRectangleInnerOffset = 6;

            this._rectangles = [
                new Geom.Rect(this._startPoint, rectangleWidth, topRectHeight),
                new Geom.Rect(new Geom.Vect(this._startPoint.x, this._startPoint.y + topRectHeight + spaceBetweenRects), rectangleWidth, bottomRectHeight),
                new Geom.Rect(new Geom.Vect(this._startPoint.x + bottomRectangleInnerOffset, this._startPoint.y + topRectHeight + spaceBetweenRects + bottomRectangleInnerOffset),
                    rectangleWidth - bottomRectangleInnerOffset*2, bottomRectHeight - bottomRectangleInnerOffset*2)
            ];

            let barWidth = 4,
                barGap = 8;
            this._bars = [
                new Geom.Rect(new Geom.Vect(this._startPoint.x + barGap, this._startPoint.y), barWidth, topRectHeight),
                new Geom.Rect(new Geom.Vect(this._startPoint.x, this._startPoint.y + barGap), rectangleWidth, barWidth),
                new Geom.Rect(new Geom.Vect(this._startPoint.x + rectangleWidth - barGap - barWidth, this._startPoint.y), barWidth, topRectHeight),
                new Geom.Rect(new Geom.Vect(this._startPoint.x, this._startPoint.y + topRectHeight - barGap - barWidth), rectangleWidth, barWidth)
            ];
        }

        _getSquares(direction) {
            let self = this,
                rect = this._getBox();
            let squareSide = this.params.squaresNumber > 3 ? 25 : 40;
            squareSide = (squareSide >= 40 && rect.width - squareSide < 15) ? 30 : squareSide;

            let squareWidth = this.params.squareWidth ? this.params.squareWidth : squareSide;

            let distance = this.params.squareDistance ? this.params.squareDistance : 10,
                padSide = rect.width/2 - squareWidth/2 + this.getLineWidth(),
                squareHeight = this.params.squareHeight === 'as-width' ? squareWidth : ((rect.height - 25 * 2) - distance * (this.params.squaresNumber - 1)) / this.params.squaresNumber,
                padTop = (rect.height - (this.params.squaresNumber * squareHeight + distance * (this.params.squaresNumber - 1))) / 2;

            this._squares = [];

            if(direction === 'left')
                padSide = 15;
            if(direction === 'right')
                padSide = rect.width - squareWidth - 15;

            this._startPoint = new Geom.Vect(padSide, padTop);

            for(let i = 0; i < this.params.squaresNumber; i++) {
                let startPoint = this._startPoint.copy();
                startPoint.add(new Geom.Vect(0, i * (squareHeight + distance)));
                let square = new Geom.Rect(startPoint, squareWidth, squareHeight);
                self._squares.push(square);
            }
        }

        _getLinesSemicircle(direction) {
            this._bezierLines = [];
            this._lines = [];
            this._lineLength = 0;
            let rect = this._getBox();
            let padSide = 20,
                h = 23,
                parentHeight = this.getParentDimension('height'),
                height = 140;

            if(parentHeight >= 160 && parentHeight < 180) height = 130;
            if(parentHeight >= 210 && parentHeight <= 237) height = 160;

            let padTop = (parentHeight - height) / 2;

            switch (direction) {
                case 'left': {
                    this._startPoint = new Geom.Vect(padSide, padTop);
                    this._endPoint = new Geom.Vect(padSide, rect.height - padTop);

                    this._offsetX = h*2;
                    this._offsetY = (this._endPoint.y - this._startPoint.y) / 2;

                    let points = [
                        this._startPoint.x + this._offsetX, this._startPoint.y + this._offsetY,
                        this._startPoint.x + this._offsetX, this._endPoint.y - this._offsetY,
                        this._endPoint.x,
                        this._endPoint.y
                    ];
                    this._bezierLines.push(points);
                    break;
                }
                case 'right': {
                    this._startPoint = new Geom.Vect(rect.width - padSide, padTop);
                    this._endPoint = new Geom.Vect(rect.width - padSide, rect.height - padTop);

                    this._offsetX = -h*2;
                    this._offsetY = (this._endPoint.y - this._startPoint.y) / 2;

                    let points = [
                        this._startPoint.x + this._offsetX, this._startPoint.y + this._offsetY,
                        this._startPoint.x + this._offsetX, this._endPoint.y - this._offsetY,
                        this._endPoint.x,
                        this._endPoint.y
                    ];
                    this._bezierLines.push(points);
                    break;
                }
            }
        }

        _getLinesCurved(direction) {
            this._bezierLines = [];
            this._lines = [];
            this._lineLength = 0;
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height');
            let padTop = 20,
                padSide = 20,
                rectHeight = 140;

            if(parentHeight >= 160 && parentHeight < 180) rectHeight = 130;
            if(parentHeight >= 210 && parentHeight <= 2370) rectHeight = 160;

            padTop = (parentHeight -rectHeight) / 2;

            this._lineLength = 24;
            this._offsetX = padSide / 1.2;
            this._offsetY = rectHeight / 2;

            switch (direction) {
                case 'left': {
                    this._startPoint = new Geom.Vect(padSide, padTop);
                    this._endPoint = new Geom.Vect(padSide, padTop + rectHeight);
                    let points = [
                        this._startPoint.x + this._offsetX, this._startPoint.y + this._offsetY,
                        this._startPoint.x + this._offsetX, this._endPoint.y - this._offsetY,
                        this._endPoint.x,
                        this._endPoint.y
                    ];
                    this._bezierLines.push(points);
                    this._bezierLines.push(points);
                    break;
                }
                case 'right': {
                    this._startPoint = new Geom.Vect(rect.width - padSide, padTop);
                    this._endPoint = new Geom.Vect(rect.width - padSide, padTop + rectHeight);

                    this._lineLength = -1 * this._lineLength ;

                    this._offsetX = -1 * this._offsetX;

                    let points = [
                        this._startPoint.x + this._offsetX, this._startPoint.y + this._offsetY,
                        this._startPoint.x + this._offsetX, this._endPoint.y - this._offsetY,
                        this._endPoint.x,
                        this._endPoint.y
                    ];
                    this._bezierLines.push(points);
                    this._bezierLines.push(points);
                    break;
                }
            }
        }

        _getLinesSinRectangle() {
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height'),
                parentWidth = this.getParentDimension('width');
            let padTop = 30;
            this._shapeHeight = parentHeight - (2*padTop);
            this._shapeWidth = (parentWidth > 79) ? 60 : 40;
            this._startPoint = new Geom.Vect((rect.width - this._shapeWidth) / 2 + this.getLineWidth(), padTop);
            this._sinHeight = parentWidth / 30;
        }

        _getLinesRectangle(direction) {
            this._bezierLines = [];
            this._lines = [];
            this._lineLength = 0;
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height');
            let padTop = 20,
                padSide = 20,
                rectWidth = 15,
                rectHeight = 100;

            if(parentHeight >= 160 && parentHeight < 180) rectHeight = 80;
            if(parentHeight >= 210 && parentHeight <= 2370) rectHeight = 120;

            switch (direction) {
                case 'left': {
                    this._startPoint = new Geom.Vect(padSide, padTop);
                    this._rectangle = new Geom.Rect(this._startPoint, rectWidth, rectHeight);

                    break;
                }
                case 'right': {
                    this._startPoint = new Geom.Vect(rect.width - padSide - rectWidth, padTop);
                    this._rectangle = new Geom.Rect(this._startPoint, rectWidth, rectHeight);

                    break;
                }
            }
        }

        _getLinesMiddleRectangle() {
            this._bezierLines = [];
            this._lines = [];
            this._lineLength = 0;
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height');
            let padTop =  20,
                rectWidth = 24,
                rectHeight = 140;

            if(parentHeight >= 160 && parentHeight < 180) rectHeight = 130;
            if(parentHeight >= 210 && parentHeight <= 2370) rectHeight = 160;

            padTop = (parentHeight - rectHeight) / 2;

            this._startPoint = new Geom.Vect(rect.width/2 - rectWidth/2 + this.getLineWidth(), padTop);
            this._rectangle = new Geom.Rect(this._startPoint, rectWidth, rectHeight);
        }

        getParentDimension(dimension) {
            let parentBlock = this.parent,
                adj = 0, dimensionValue = 0;
            while (dimensionValue === 0) {
                if (parentBlock[dimension])
                    dimensionValue = parentBlock[dimension];
                else parentBlock = parentBlock.parent;
            }
            if(parentBlock.getMeasurementByDimension(dimension)) {
               adj = parentBlock.getMeasurementByDimension(dimension)._getAdjustmentSum();
            }
            return dimensionValue + adj;
        }

        _getCrosslines(direction) {
            this._lines = [];
            let rect = this._getBox(),
                parentHeight = this.getParentDimension('height'),
                parentWidth = this.getParentDimension('width');
            let padBetweenLines = 10,
                padSide = padBetweenLines,
                padBetweenHorizLines = padBetweenLines * 1.5,
                padTop = padBetweenHorizLines * 1.5;

            let horizLineWidth = parentWidth,
                vertLineHeight = parentHeight;

            this._startPoint = new Geom.Vect(padSide, 0);

            //middleLines
            this._lines = this._lines.concat(
                this.getParallelLines(
                    new Geom.Vect(0, (rect.height - padBetweenHorizLines) / 2),
                    padBetweenHorizLines, 0, horizLineWidth)
            );

            //topLines
            this._lines = this._lines.concat(
                this.getParallelLines(
                    new Geom.Vect(0, padTop),
                    padBetweenHorizLines, 0, horizLineWidth)
            );

            //bottomLines
            this._lines = this._lines.concat(
                this.getParallelLines(
                    new Geom.Vect(0, rect.height - padTop - padBetweenHorizLines),
                    padBetweenHorizLines, 0, horizLineWidth)
            );

            switch (direction) {
                case 'right': {
                    this._startPoint = new Geom.Vect(rect.width - padSide - padBetweenLines, 0);
                    break;
                }
            }
            this._lines = this._lines.concat(
                this.getParallelLines(
                    this._startPoint,
                    padBetweenLines, vertLineHeight, 0)
            );
        }

        getParallelLines(startPoint, padBetween, height = 0, width = 0) {
            if(!height && !width) {
                return null;
            }
            let line1, line2,
                secondPoint = startPoint.copy(),
                moveVect = new Geom.Vect(width ? 0 : padBetween, width ? padBetween: 0);
            secondPoint.add(new Geom.Vect(width ? width : 0, width ? 0 : height));
            line1 = new Geom.Segment(startPoint, secondPoint);
            line2 = line1.copy();
            line2.move(moveVect);
            return [line1, line2];
        }

    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: DoorShape
    };
});
