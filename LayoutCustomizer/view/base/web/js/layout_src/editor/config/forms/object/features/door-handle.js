define([
    '../../../../../blocks/features/door-handle',
    '../../../../../data/helper',
    './_over_children'
], function(DoorHandle, DataHelper, OverChildren) {
    return DataHelper.merged(OverChildren, {
        fields: [
            {
                name: 'params.type', label: 'Type', type: 'select',
                params: {options: DoorHandle.Params.type.options}
            },
            {
                name: 'params.side', label: 'Side', type: 'select',
                params: {options: DoorHandle.Params.side.options}
            },
            { name: 'params.sidePosition', label: 'Position from the side', type: 'number' },
            { name: 'params.bottomPosition', label: 'Position from bottom to handle', type: 'number' },
            { name: 'params.is_lock', label: 'Handle lock', type: 'checkbox'},
            {
                name: 'params.destLock', label: 'Distance between handle and lock (from center to center)',  type: 'number',
                depends: [
                    {'params.is_lock': {'=': true}}
                ]
            },
            {
                name: 'params.lockPosition', label: 'Lock Position',  type: 'select',
                params: {options: DoorHandle.Params.lockPosition.options},
                depends: [
                    {'params.is_lock': {'=': true}}
                ]
            }
        ]
    });
});
