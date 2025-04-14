define([
    '../helper',
    '../../render/helper/scale'
], function(Helper, ScaleHelper) {

    function customize(customizer, drawer, measurement) {
        let context = customizer.context,
            input = Helper.getInput(context, drawer, measurement),
            rect = input.element.getBoundingClientRect();

        let line = measurement.getConnectionLine(),
            scaledLine = ScaleHelper.scaleSegment(drawer.offset, drawer.getScale(), line);

        // let gap = 4;
        let epsilon = 0.0001, // TODO: move
            left = 0,
            top = 0;
        if (Math.abs(line.p1.y - line.p2.y) < epsilon) {
            // Horizontal line
            left = Math.min(scaledLine.p1.x, scaledLine.p2.x)
                + (scaledLine.length() - rect.width) / 2;
            // center
            top = input.element.classList.contains('non-customizable') ?
                scaledLine.p1.y - parseInt(window.getComputedStyle(input.element.getElementsByClassName('measurement-tooltip')[0])
                                        .getPropertyValue('margin-top'), 10) :
                scaledLine.p1.y - rect.height / 2;
            // top = scaledLine.p1.y - rect.height - gap; // over line
        } else {
            // Vertical line
            left = scaledLine.p1.x - rect.width / 2; // center
            // left = scaledLine.p1.x - rect.width - gap; // left of line
            top = Math.min(scaledLine.p1.y, scaledLine.p2.y)
                + (scaledLine.length() - rect.height) / 2;
        }

        input.element.style.top = top.toString() + 'px';
        input.element.style.left = left.toString() + 'px';

    }

    return {customize: customize};
});
