define([
    '../../../blocks/color',
    '../../../blocks/geometry',
    '../feature',
    '../helper'
], function(Color, Geom, FeatureDrawer, Helper) {

    class FireEscapeDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
            this._image = null;
        }

        _draw(drawer, feature) {
            if (!feature.params.is_fire_escape) return;

            let imageBox = feature.getImageBox();
            if (this._image === null) {
                // First load
                let image = this._getImage(drawer, imageBox.width, imageBox.height);
                if (image) {
                    // Save positioning data
                    let posData = drawer.getPositioningData();
                    image.addEventListener('load', function(event) {
                        // Save current pos. data
                        let currentPosData = drawer.getPositioningData();
                        // Apply old positioning data
                        drawer.setPositioningData(posData);
                        Helper.drawImage(drawer, image, imageBox.pos);
                        // Restore pos. data
                        drawer.setPositioningData(currentPosData);
                        this._image = image;
                    }.bind(this));
                }
                this._image = false;

            } else if (this._image) {
                // Loaded image
                Helper.drawImage(drawer, this._image, imageBox.pos);
            }
        }

        _getImage(drawer, width, height) {
            let image = false,
                url = drawer.getAssetUrl('top-guided-w-fire-escape');
            if (url) {
                image = new Image(width, height);
                image.src = url;
            }
            return image;
        }
    }

    return {Drawer: FireEscapeDrawer};
});
