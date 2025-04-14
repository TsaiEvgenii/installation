define([
    './drawable',
    './geometry'
], function(Drawable, Geom) {

    var Type = 'measurement';

    // NOTE: these objects were directly drawn until block shapes were added.
    // Block shapes can have multiple measurements that have to be integrated
    // with block measurements added manually, for placement calculation.
    // Block measurements (and block shapes) now return objects from src/measurements/
    // (naming things is hard) that are processed uniformly by src/measurements/manager.

    class Measurement extends Drawable.Base {
        constructor(measurementType) {
            super(Type);
            this._measurementType = measurementType;
            this._isCustomizable = false;
            this._name = null;
            this._params = {};
        }

        getMeasurement() { return null; }

        place() {}

        getBoundingRect() {
            let measurement = this.getMeasurement();
            return measurement
                ? measurement.getBoundingRect()
                : null;
        }

        getValue() {
            throw "`getValue()' is not implemented";
        }

        setValue() {
            throw "`setValue()' is not implemented";
        }

        get measurementType() { return this._measurementType; }

        get isCustomizable() { return this._isCustomizable; }
        set isCustomizable(isCustomizable) { this._isCustomizable = isCustomizable; }

        get name() { return this._name; }
        set name(name) {
            if(name === null || name.length === 0)
                name = 'field' + this._field + this._objectId._index;
            this._name = name;
        }

        get params() { return this._params; }
        set params(params) { this._params = params; }
    }

    return {
        Type: Type,
        Base: Measurement
    };
});
