define([
    '../../../../../blocks/shapes/trunc-triang'
], function(TruncatedTriangular) {
    return {
        fields: [
            {name: 'params.cut_side', label: 'Cut Side', type: 'select',
             params: {options: TruncatedTriangular.Options.cut_side}},
            // Top
            {name: 'params.top_param', label: 'Top Width Param', type: 'select', params: {
                options: TruncatedTriangular.Options.top_param}},
            {name: 'params.top_width', label: 'Top Width', type: 'number', params: {
                isNullable: true, nullText: 'Center'
            }},
            {name: 'params.show_top_width', label: 'Show top width', type: 'checkbox'},
            {name: 'params.is_top_width_customizable', label: 'Is Width Customizable', type: 'checkbox'},
            {name: 'params.top_width_name', label: 'Width Name', type: 'text'},
            {name: 'params.top_width_min', label: 'Min. Width', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_top_width_customizable': {'=': true}}]},
            {name: 'params.top_width_max', label: 'Max. Width', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_top_width_customizable': {'=': true}}]},
            // Side
            {name: 'params.side_param', label: 'Side Height Param', type: 'select', params: {
                options: TruncatedTriangular.Options.side_param}},
            {name: 'params.side_height', label: 'Side Height', type: 'number', params: {
                isNullable: true, nullText: 'Center'
            }},
            {name: 'params.show_side_height', label: 'Show side height', type: 'checkbox'},
            {name: 'params.is_side_height_customizable', label: 'Is Height Customizable', type: 'checkbox'},
            {name: 'params.side_height_name', label: 'Height Name', type: 'text'},
            {name: 'params.side_height_min', label: 'Min. Height', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_side_height_customizable': {'=': true}}]},
            {name: 'params.side_height_max', label: 'Max. Height', type: 'number',
             params: {isNullable: true, nullText: 'None'},
             depends: [{'params.is_side_height_customizable': {'=': true}}]}
        ]
    }
});
