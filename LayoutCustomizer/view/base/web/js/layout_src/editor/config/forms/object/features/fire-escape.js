define([
    '../../../../../blocks/features/fire-escape',
    '../../../../../data/helper',
    './_over_children'
], function(FireEscape, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.is_fire_escape', label: 'Is Fire Escape', type: 'checkbox'}
        ]
    });
});
