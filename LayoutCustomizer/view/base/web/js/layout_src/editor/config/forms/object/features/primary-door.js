define([
    '../../../../../blocks/features/primary-door',
    '../../../../../data/helper',
    './_over_children'
], function(PrimaryDoor, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.is_primary', label: 'Is Primary', type: 'checkbox'}
        ]
    });
});
