define([
    '../shapes/all'
], function(ShapeList) {

    function make(type, params = {}) {
        let shape = new ShapeList[type].Shape();
        Object.assign(shape.params, params);
        return shape;
    }

    function makeDefault() {
        return make('rectangle');
    }

    return {
        make: make,
        makeDefault: makeDefault
    };
});
