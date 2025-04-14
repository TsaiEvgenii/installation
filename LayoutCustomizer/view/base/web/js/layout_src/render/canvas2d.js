define([
    '../blocks/color',
    '../blocks/geometry',
    './drawer',
    './canvas2d/block',
    './canvas2d/shape',
    './canvas2d/shapes/all',
    './canvas2d/feature',
    './canvas2d/features/all',
    './canvas2d/measurement',
    './canvas2d/measurements/all'
], function(
    Color, Geom, Base,
    BlockDrawer,
    ShapeDrawer, ShapeDrawerList,
    FeatureDrawer, FeatureDrawerList,
    MeasurementDrawer, MeasurementDrawerList) {

    class Drawer extends Base.Base {
        constructor(canvas) {
            super();

            this._canvas = canvas;
            this._offset = new Geom.Vect(0, 0);
            this._backgroundColor = null;

            this.add('block', new BlockDrawer.Drawer());
            this.add('shape', new ShapeDrawer.Drawer(ShapeDrawerList));
            this.add('feature', new FeatureDrawer.Drawer(FeatureDrawerList));
            this.add('measurement', new MeasurementDrawer.Drawer(MeasurementDrawerList));

            this.scaleMultiplier = 1.5;
        }

        setSmoothing(smooth) {
            this.context2d.imageSmoothingEnabled = smooth;
        }

        exportImage() {
            return this._canvas.toDataURL('image/png');
        }

        getPositioningData() {
            let data = super.getPositioningData();
            data.offset = this.offset.copy();
            return data;
        }

        setPositioningData(data) {
            super.setPositioningData(data);
            this.offset = data.offset.copy();
        }

        _center(object) {
            let offset = new Geom.Vect(0, 0),
                canvas = this._canvas,
                rect = object.getBoundingRect();


            let scale = this.getScale();
            if (object.pos.x === null) {
                let scaledWidth = rect.width * scale;
                offset.x = (canvas.width - scaledWidth) / 2
                    + (object.box.pos.x - rect.pos.x) * scale;
            }
            if (object.pos.y === null) {
                let scaledHeight = rect.height * scale;
                offset.y = (canvas.height - scaledHeight) / 2
                    + (object.box.pos.y - rect.pos.y) * scale;
            }

            this._offset = offset;
        }

        reset() {
            let canvas = this._canvas,
                ctx = this.context2d;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (this._backgroundColor !== null) {
                let fillStyle = ctx.fillStyle;
                ctx.fillStyle = Color.prepare(this._backgroundColor);
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = fillStyle;
            }
        }

        resize(width, height) {
            let canvas = this._canvas;
            canvas.width = width;
            canvas.height = height;
        }

        get canvas() { return this._canvas; }
        set canvas(canvas) { this._canvas = canvas; }
        get context2d() { return this._canvas.getContext('2d'); }
        get offset() { return this._offset; }
        set offset(offset) { this._offset = offset; }
        get backgroundColor() { return this._backgroundColor; }
        set backgroundColor(color) { this._backgroundColor = color; }
    }

    return {Drawer: Drawer};
});
