define([
    '../../../ui/widgets/toolbar/scale'
], function(Base) {

    class Scale extends Base.Widget {
        constructor(context) {
            super(context, context.config.scaleOptions || []);
        }
    }

    return {
        Type: Base.Type,
        Name: Base.Name,
        Widget: Scale
    }
});
