define([
    '../helper',
    '../../render/helper/scale'
], function(Helper, ScaleHelper) {

    function customize(customizer, drawer, measurement) {
        let context = customizer.context,
            input = Helper.getInput(context, drawer, measurement),
            inputRect = input.element.getBoundingClientRect();

        let line = measurement.getLine(),
            center = line.center(),
            pos = ScaleHelper.scaleVect(drawer.offset, drawer.getScale(), center);

        input.element.style.left = pos.x.toString() - (inputRect.width / 2) + 'px';
        input.element.style.top = pos.y.toString() - (inputRect.height / 2) + 'px';
    }

    return {customize: customize};
});
