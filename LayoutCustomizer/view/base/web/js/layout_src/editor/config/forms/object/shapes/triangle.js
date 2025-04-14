define([
    '../../../../../blocks/shapes/triangle'
], function(Triangle) {
    return {
        fields: [
            {
                name: 'params.base', label: 'Base', type: 'select',
                params: {options: Triangle.Options.base}
            },
            {
                name: 'params.param_type', label: 'Parameters', type: 'select',
                params: {options: Triangle.Options.param_type}
            },
            {name: 'params.show_offsets', label: 'Show offsets', type: 'checkbox'},

            // Angle
            {
                name: 'params.base_angle', label: 'Base Angle', type: 'select',
                params: {options: Triangle.Options.base_angle},
                depends: [{'params.param_type': {'=': 'angle'}}]
            },
            {
                name: 'params.angle', label: 'Angle', type: 'number',
                params: {isNullable: true, nullText: 'Center'},
                depends: [{'params.param_type': {'=': 'angle'}}]
            },
            {
                name: 'params.offset_from', label: 'Offset From', type: 'select',
                params: {options: Triangle.Options.offset_from},
                depends: [{'params.param_type': {'=': 'offset'}}]
            },
            {
                name: 'params.offset', label: 'Offset', type: 'number',
                params: {isNullable: true, nullText: 'Center'},
                depends: [{'params.param_type': {'=': 'offset'}}]
            },
            {
                name: 'params.is_customizable', label: 'Is Customizable', type: 'checkbox'
            },
            {
                name: 'params.angle_name', label: 'Angle Name', type: 'text'
            },
            {
                name: 'params.offset_name', label: 'Offset Name', type: 'text'
            },
            {
                name: 'params.angle_min', label: 'Min. Angle', type: 'number',
                params: {isNullable: true, nullText: 'None'},
                depends: [{'params.param_type': {'=': 'angle'}, 'params.is_customizable': {'=': true}}]
            },
            {
                name: 'params.angle_max', label: 'Max. Angle', type: 'number',
                params: {isNullable: true, nullText: 'None'},
                depends: [{'params.param_type': {'=': 'angle'}, 'params.is_customizable': {'=': true}}]
            },
            {
                name: 'params.offset_min', label: 'Min. Offset', type: 'number',
                params: {isNullable: true, nullText: 'None'},
                depends: [{'params.param_type': {'=': 'offset'}, 'params.is_customizable': {'=': true}}]
            },
            {
                name: 'params.offset_max', label: 'Max. Offset', type: 'number',
                params: {isNullable: true, nullText: 'None'},
                depends: [{'params.param_type': {'=': 'offset'}, 'params.is_customizable': {'=': true}}]
            }
        ]
    };
});
