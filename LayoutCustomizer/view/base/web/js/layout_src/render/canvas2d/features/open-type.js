define([
    '../../../blocks/color',
    '../feature',
    '../helper'
], function(Color, FeatureDrawer, Helper) {

    class OpenTypeDrawer extends FeatureDrawer.Base {
        constructor() {
            super();
            this._image = null;
        }

        _draw(drawer, feature) {
            let ctx = drawer.context2d,
                rect = feature.parent.innerBox(),
                lines = feature.getLines();

            // Set line Color
            {
                let color = feature.getLineColor();
                if (color !== null) {
                    ctx.strokeStyle = Color.prepare(color);
                }
            }

            // Set line width
            {
                let lineWidth = feature.getLineWidth();
                if (lineWidth != null) {
                    ctx.lineWidth = lineWidth;
                }
            }

            if(feature.params.type == 'tilt-slide-left'
                || feature.params.type == 'tilt-slide-right'
            ) {
                feature._arrow.forEach(Helper.drawLine.bind(null, drawer));
            }

            // Set line dash
            ctx.setLineDash(feature.getLineDash());
            // Set line cap
            ctx.lineCap = 'round';

            // Draw lines
            lines.forEach(Helper.drawLine.bind(null, drawer));

            if(feature.params.type == 'top-guided-w-fire-escape' && feature._imageBox !== null) {
                let imageBox = feature._imageBox;
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

    return {Drawer: OpenTypeDrawer};
});
