define([
    '../../../../../blocks/shapes/semicircle'
], function(Semicircle) {
    return {
        fields: [
            {name: 'params.base', label: 'Base', type: 'select', params: {
                options: Semicircle.Options.base}},
            {name: 'params.width', label: 'Width', type: 'number', params: {
                isNullable: true, nullText: 'Block'
            }},
            // {name: 'params.width_max', label: 'Max Width', type: 'number'},
        ]
    };
});
