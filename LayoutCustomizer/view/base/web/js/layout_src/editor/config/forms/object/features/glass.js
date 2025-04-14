define([
    '../../../../../data/helper',
    './_over_children'
], function(DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.color', label: 'Color', type: 'color', params: {
                isNullable: true, nullText: 'None'
            }},
            {name: 'params.opacity', label: 'Opacity', type: 'number', params: {
                input: {step: 0.01}
            }}
        ]
    });
});
