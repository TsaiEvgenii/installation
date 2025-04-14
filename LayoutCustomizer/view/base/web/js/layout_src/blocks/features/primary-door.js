define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'primary-door',
        Name = 'Primary Door',
        Params = {
            is_primary: {label: 'Is Primary', type: 'checkbox'}
        };

    class PrimaryDoor extends Feature.Base {
        constructor() {
            super(Type);
            this.params.is_primary = true;
            this._imageBox = new Geom.Rect(new Geom.Vect(0, 0), 20, 20);
        }

        place() {
            let imageBox = this._imageBox,
                box = this.parent.box;
            imageBox.pos.x = box.pos.x + (box.width - imageBox.width) / 2,
            imageBox.pos.y = box.pos.y + (box.height - imageBox.height) / 2;
        }

        getImageBox() {
            return this._imageBox;
        }
    }

    return {
        Type: Type,
        Name: Name,
        Params: Params,
        Feature: PrimaryDoor
    };
});
