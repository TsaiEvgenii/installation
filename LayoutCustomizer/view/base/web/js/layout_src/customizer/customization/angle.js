define([
    '../helper',
    '../../render/helper/scale'
], function(Helper, ScaleHelper) {

    function customize(customizer, drawer, measurement) {
        let context = customizer.context,
            input = Helper.getInput(context, drawer, measurement),
            inputRect = input.element.getBoundingClientRect();

        let radius = measurement.radius,
            apex = measurement.apex,
            point1 = measurement.point1,
            point2 = measurement.point2,
            vect = point1.diff(apex).sum(point2.diff(apex)).resized(radius * 2),
            pos = ScaleHelper.scaleVect(drawer.offset, drawer.getScale(), apex.sum(vect));

        input.element.style.left = pos.x.toString() - (inputRect.width / 2) + 'px';
        input.element.style.top = pos.y.toString() - (inputRect.height / 2) + 'px';
    }

    return {customize: customize};
});
