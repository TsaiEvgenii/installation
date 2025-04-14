define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'door-handle',
        Name = 'Door Handle',
        Params = {
            // is_handle: {label: 'Door Handle', type: 'checkbox'},
            type: {label: 'Handle type', type: 'select', options: [
                    {name: 'None', value: 'none'},
                    {name: 'Handle type U', value: 'type-u'},
                    {name: 'Handle type L', value: 'type-l'},
                    {name: 'Handle type C', value: 'type-c'}
            ]},
            side: {label: 'Side', type: 'select', options: [
                    {name: 'Left', value: 'left'},
                    {name: 'Right', value: 'right'}
            ]},
            sidePosition: {label: 'Side position', type: 'number'},
            bottomPosition: {label: 'bottom position', type: 'number'},
            is_lock: {label: 'Handle lock', type: 'checkbox'},
            destLock: {label: 'Distance between handle and lock (from center to center)', type: 'number'},
            lockPosition: {label: 'Lock Position', type: 'select', options: [
                        {name: 'Above', value: 'above'},
                        {name: 'Below', value: 'below'}
            ]}
        };

    class DoorHandle extends Feature.Base {
        constructor() {
            super(Type);
            this.params.type = 'type-u';
            this.params.side = 'right';
            this.params.sidePosition = 3;
            this.params.bottomPosition = 103.6;

            this.params.is_lock = true;
            this.params.destLock = 10;
            this.params.lockPosition = 'below';
        }

        place() {
            this._getDoorHandle();
            if(this.params.is_lock) this._getDoorLock();
        }

        _getDoorHandle () {
            let box = this._getBox(),
                circleRadius = 4,
                handleWidth = 15,
                handleHeight = 2,
                padSide = this.params.sidePosition;

            let circleCenter = box._pos.copy(), rectPos;

            switch(this.params.side) {
                case 'right':
                    circleCenter.add(new Geom.Vect( box.width - (padSide + circleRadius), box.height - this.params.bottomPosition));
                    rectPos = circleCenter.copy();
                    rectPos.add(new Geom.Vect(-handleWidth, -handleHeight/2));
                    break;
                case 'left':
                    circleCenter.add(new Geom.Vect(padSide + circleRadius, box.height - this.params.bottomPosition));
                    rectPos = circleCenter.copy();
                    rectPos.add(new Geom.Vect(0, -handleHeight/2));
                    break;
                default:
                    throw "Invalid type: `" + this.params.side + "'";
            }

            this._circle = new Geom.Circle(circleCenter, circleRadius);

            switch (this.params.type) {
                case 'none':
                    this._circle = null;
                    this._handle = null;
                    break;
                case 'type-u':
                    this._handle = new Geom.Rect(rectPos, handleWidth, handleHeight);
                    break;
                case 'type-l':
                    this._handle = new Geom.Rect(rectPos, handleWidth, handleHeight);
                    break;
                case 'type-c':
                    handleHeight = handleHeight * 1.3;
                    rectPos.add(new Geom.Vect(0, -handleHeight * 0.15));
                    let linesPoints = this.params.side === 'left' ?
                        [
                            // rectPos,
                            new Geom.Vect(rectPos.x, rectPos.y),
                            new Geom.Vect(rectPos.x + handleWidth, rectPos.y + handleHeight/2),
                            new Geom.Vect(rectPos.x, rectPos.y + handleHeight),
                        ] :
                        [
                            new Geom.Vect(rectPos.x + handleWidth, rectPos.y),
                            new Geom.Vect(rectPos.x, rectPos.y + handleHeight/2),
                            new Geom.Vect(rectPos.x + handleWidth, rectPos.y + handleHeight),
                        ];
                    // handleHeight = handleHeight * 1.3;
                    // rectPos.add(new Geom.Vect(0, -handleHeight * 0.15));
                    // let linesPoints = this.params.side === 'left' ?
                    //     [
                    //         rectPos,
                    //         new Geom.Vect(rectPos.x + handleWidth/2, rectPos.y),
                    //         new Geom.Vect(rectPos.x + handleWidth, rectPos.y + handleHeight/2),
                    //         new Geom.Vect(rectPos.x + handleWidth/2, rectPos.y + handleHeight),
                    //         new Geom.Vect(rectPos.x, rectPos.y + handleHeight)
                    //     ] :
                    //     [
                    //         new Geom.Vect(rectPos.x + handleWidth, rectPos.y),
                    //         new Geom.Vect(rectPos.x + handleWidth/2, rectPos.y),
                    //         new Geom.Vect(rectPos.x, rectPos.y + handleHeight/2),
                    //         new Geom.Vect(rectPos.x + handleWidth/2, rectPos.y + handleHeight),
                    //         new Geom.Vect(rectPos.x  + handleWidth, rectPos.y + handleHeight)
                    //     ];
                    this._handle = linesPoints;
                    break;
            }
        }

        _getDoorLock() {
            if(this.params.type === 'none') return;
            let lockEllipseR = 2,
                lockDest = this.params.destLock;

            lockDest = this.params.lockPosition === 'below' ? lockDest : -lockDest;

            this._lockCircle = this._circle.copy();
            this._lockCircle.center.add(new Geom.Vect(0, lockDest));
            let ellipseCenter = this._lockCircle.center.copy();
            ellipseCenter.add(new Geom.Vect(-0.5, 0));
            this._lockEllipse = new Geom.Circle(ellipseCenter, lockEllipseR);
        }

        getCircle() {
            return this._circle;
        }

        getHandle() {
            return this._handle;
        }

        getLock() {
            return this._lockCircle;
        }

        getEllipse() {
            return this._lockEllipse;
        }

        _getBox() {
            let halfLineWidth = this.getLineWidth() / 2;
            return this.parent.shape.getFeatureBox()
                .grown(new Geom.Vect(-halfLineWidth, -halfLineWidth));
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
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: DoorHandle
    };
});
