define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    var Type = 'crossbars',
        Name = 'Crossbars',
        Params = {
            numHorizontal: {label: 'Num. Horizontal', type: 'number'},
            numVertical: {label: 'Num. Vertical', type: 'number'},
            color: {label: 'Color', type: 'color', nullable: true},
            lineColor: {label: 'Line Color', type: 'color', nullable: true},
            width: {label: 'Width', type: 'number'},
            placement: {label: 'Placement', type: 'select', options: [
                    {name: 'Center', value: 'none'},
                    {name: 'Middle Bottom', value: 'middle-bottom'},
                    {name: 'Middle Top', value: 'middle-top'}
            ]}
        };

    class Crossbars extends Feature.Base {
        constructor() {
            super(Type);
            // default params
            this.params.numHorizontal = 1;
            this.params.numVertical   = 1;
            this.params.placement = 'none';
            this.params.color = null;
            this.params.lineColor = null;
            this.params.width = 10;
            // calculated properties
            this._box = new Geom.Rect();
            this._holes = []; // Rect[] holes to cut in box
            this._lines = []; // Line[] border lines
        }

        place() {
            // Set box
            let box = this.parent.shape.getFeatureBox();
            this._box = box;

            // Prepare params
            let numHorizontal = Math.max(0, this.params.numHorizontal),
                numVertical = Math.max(0, this.params.numVertical),
                width = parseFloat(this.params.width);

            // Calc. hole dimensions
            let holeWidth = (box.width - (width * numHorizontal)) / (numHorizontal + 1);
            let holeHeight = (box.height - (width * numVertical)) / (numVertical + 1);

            // Add holes and lines for each cell
            let holes = [],
                lines = [];
            if(numVertical == 1) {
                switch(this.params.placement) {
                    case 'none':
                        this.calculateCrossBars(box, holeWidth, holeHeight, width, numVertical, numHorizontal);
                        break;
                    case 'middle-bottom':
                        holes = this.getMiddleHoles(0.70, box, width, holeHeight, holeWidth);
                        lines = [holes[0].bottom(), holes[1].top()];
                        this._holes = holes;
                        this._lines = lines;
                        if(numHorizontal > 0) {
                            this._holes = this.divideMiddleHoles(holeWidth, width, numHorizontal);
                            this._lines = this.getDividedMiddleHolesLines(numHorizontal);
                        }
                        break;
                    case 'middle-top':
                        holes = this.getMiddleHoles(0.30, box, width, holeHeight, holeWidth);
                        lines = [holes[0].bottom(), holes[1].top()];
                        this._holes = holes;
                        this._lines = lines;
                        if(numHorizontal > 0) {
                            this._holes = this.divideMiddleHoles(holeWidth, width, numHorizontal);
                            this._lines = this.getDividedMiddleHolesLines(numHorizontal);
                        }
                        break;
                }
            } else {
                this.calculateCrossBars(box, holeWidth, holeHeight, width, numVertical, numHorizontal);
            }
        }

        divideMiddleHoles(holeWidth, width, numHorizontal) {
            return this._holes.flatMap((hole) => {
                let holes = [];
                for(let ind = 0; ind <= numHorizontal; ind++) {
                    let startVect = hole.topLeft().sum(new Geom.Vect((holeWidth + width) * ind, 0));
                    holes.push(new Geom.Rect(startVect, holeWidth, hole.height))
                }
                return holes;
            })
        }

        getDividedMiddleHolesLines(numHorizontal) {
            let middleHolesIndexesStart = numHorizontal;
            let middleHolesIndexesEnd = (this._holes.length - 1) - middleHolesIndexesStart;
            return this._holes.flatMap((hole, index) => {
                let lines = [];
                // check if hole is the first in a row
                if(index == 0 || index % (numHorizontal + 1) === 0) {
                    lines.push(hole.right());
                }
                // check if hole is the last in a row
                else if(index > 0 && (index + 1) % (numHorizontal + 1) === 0) {
                    lines.push(hole.left());
                }
                // hole is in the middle in a row
                else {
                    lines.push(hole.left(), hole.right())
                }

                // check if hole is the first in a col
                if(index <= middleHolesIndexesStart) {
                    lines.push(hole.bottom());
                }
                // check if hole is the last in a col
                else if(index >= middleHolesIndexesEnd) {
                    lines.push(hole.top());
                }
                // check if hole is in the middle in a col
                else {
                    lines.push(hole.top(), hole.bottom());
                }

                return lines;
            })
        }

        calculateCrossBars(box, holeWidth, holeHeight, width, numVertical, numHorizontal) {
            let holes = [],
                lines = [];
            for (let i = 0; i <= numVertical; ++i) {
                for (let j = 0; j <= numHorizontal; ++j) {
                    // add hole
                    let hole = new Geom.Rect(
                        new Geom.Vect(
                            box.pos.x + j * (holeWidth + width),
                            box.pos.y + i * (holeHeight + width)),
                        holeWidth, holeHeight);
                    holes.push(hole);
                    // add border lines
                    if (i != 0) {
                        lines.push(hole.top());
                    }
                    if (j != numHorizontal) {
                        lines.push(hole.right());
                    }
                    if (i != numVertical) {
                        lines.push(hole.bottom());
                    }
                    if (j != 0) {
                        lines.push(hole.left());
                    }
                } // j
            } // i
            this._holes = holes;
            this._lines = lines;
        }

        getMiddleHoles(coef, box, width, holeHeight, holeWidth) {
            let hole1 = new Geom.Rect(
                new Geom.Vect(
                    box.pos.x,
                    box.pos.y),
                holeWidth, box.height * coef),

                hole2 = new Geom.Rect(
                    new Geom.Vect(
                        box.pos.x,
                        box.pos.y + (hole1.height + width)),
                    holeWidth, box.height - box.height * coef + width);
            return [hole1, hole2];
        }

        getBox() {
            return this._box;
        }

        getHoles() {
            return this._holes;
        }

        getColor() {
            return (this.params.color !== null)
                ? this.params.color
                : this.parent.color;
        }

        getLineColor() {
            return (this.params.lineColor !== null)
                ? this.params.lineColor
                : this.parent.borderColor;
        }

        getLines() {
            return this._lines;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: Crossbars
    };
});
