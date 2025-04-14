define([
    '../../blocks/helper/shape'
], function(ShapeHelper) {
    return [
        {from: 'name', to: 'name', type: 'string', nullable: false},
        {from: 'pos_x', to: 'pos.x', type: 'number', nullable: true},
        {from: 'pos_y', to: 'pos.y', type: 'number', nullable: true},
        {from: 'width', to: 'width', type: 'number', nullable: true},
        {from: 'height', to: 'height', type: 'number', nullable: true},
        {from: 'layout', to: 'layout', type: 'string'},
        {from: 'border', to: 'border', type: 'number'},
        {from: 'border_placement', to: 'borderPlacement', type: 'string'},
        {from: 'border_color', to: 'borderColor', type: 'string', nullable: true},
        {from: 'inner_border', to: 'innerBorder', type: 'number', nullable: true},
        {from: 'inner_border_placement', to: 'innerBorderPlacement', type: 'string'},
        {from: 'inner_border_color', to: 'innerBorderColor', type: 'string', nullable: true},
        {from: 'padding', to: 'padding', type: 'number'},
        {from: 'feature_padding', to: 'featurePadding', type: 'number', nullable: true},
        {from: 'spacing', to: 'spacing', type: 'number'},
        {from: 'color', to: 'color', type: 'string', nullable: true},
        {from: 'children', add: true, type: 'Object', list: true},
        {from: 'features', add: true, type: 'Object', list: true},
        {from: 'measurements', add: true, type: 'Object', list: true},
        {from: 'parameters', add: true, type: 'Object', list: true},
        {from: 'restrictions', add: true, type: 'Object', list: true},
        {from: 'measurement_restrictions', add: true, type: 'Object', list: true},
        {
            get: function(data) {
                return data.shape
                    ? ShapeHelper.make(data.shape, data.shape_params || {})
                    : ShapeHelper.makeDefault();
            },
            to: 'shape',
            type: 'object'
        },
        {from: 'links', add: true, type: 'Object', list: true},
        {from: 'reverse', to: 'reverse', type: 'string'}
    ];
});
