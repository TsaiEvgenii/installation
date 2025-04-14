define([
    '../../../../../blocks/shapes/vert-triang'
], function(VerticalTriangular) {
    return {
        fields: [
            // Height
            {name: 'params.height_param', label: 'Height Param', type: 'select', params: {
                options: VerticalTriangular.Options.height_param}},
            {name: 'params.height', label: 'Height', type: 'number', params: {
                isNullable: true, nullText: 'Middle'
            }},
            {name: 'params.is_height_customizable', label: 'Is Height Customizable', type: 'checkbox'},
            {name: 'params.height_name', label: 'Height Name', type: 'text'},
            {name: 'params.height_min', label: 'Min. Height', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_height_customizable': {'=': true}}]},
            {name: 'params.height_max', label: 'Max. Height', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_height_customizable': {'=': true}}]},
            // Offset
            {name: 'params.offset_param', label: 'Offset Param', type: 'select',params: {
                options: VerticalTriangular.Options.offset_param}},
            {name: 'params.offset', label: 'Offset', type: 'number', params: {
                isNullable: true, nullText: 'Middle'
            }},
            {name: 'params.is_offset_customizable', label: 'Is Offset Customizable', type: 'checkbox'},
            {name: 'params.offset_name', label: 'Offset Name', type: 'text'},
            {name: 'params.offset_min', label: 'Min. Offset', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_offset_customizable': {'=': true}}]},
            {name: 'params.offset_max', label: 'Max. Offset', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_offset_customizable': {'=': true}}]}
        ]
    };
});
