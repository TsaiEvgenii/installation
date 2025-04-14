define([
    '../../../../../blocks/features/sliding-door',
    '../../../../../data/helper',
    './_over_children'
], function(SlidingDoor, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {name: 'params.type', label: 'Door Type', type: 'select',
                    params: {options: SlidingDoor.Params.type.options}
            }
        ]
    });
});
