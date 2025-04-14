define([
    '../feature',
    '../geometry'
], function(Feature, Geom) {

    let Type = 'fire-escape',
        Name = 'Fire Escape',
        Params = {
            is_fire_escape: {label: 'Is Fire Escape', type: 'checkbox'}
        };

    class FireEscape extends Feature.Base {
        constructor() {
            super(Type);
            this.params.is_fire_escape = false;
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
        Feature: FireEscape
    };
});
