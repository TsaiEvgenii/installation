define([
    '../../../../../blocks/measurements/width'
], function(Width) {
    return {
        fields: [
            {name: 'params.adjustment1', label: 'Adjustment Left', type: 'number'},
            {name: 'params.adjustment2', label: 'Adjustment Right', type: 'number'},
            {name: 'params.offset', label: 'Offset', type: 'number'},
            {name: 'params.placement', label: 'Placement', type: 'select', params: {
                options: Width.Params.placement.options
            }},
            {name: 'objectData.param_id', label: 'Parameter', type: 'ext-param-select'},
            {name: 'isCustomizable', label: 'Is Customizable', type: 'checkbox'},
            {name: 'name', label: 'Name', type: 'text',
             params: {
                 isNullable: true, nullText: 'None'
             },
             depends: [{'isCustomizable': {'=': true}}]},
            {name: 'params.min', label: 'Min', type: 'formula',
             params: {
                 isNullable: true, nullText: 'None', objectType: 'block'
             }},
            {name: 'params.max', label: 'Max', type: 'formula',
             params: {
                 isNullable: true, nullText: 'None', objectType: 'block'
             }}
        ]
    };
});
