define([
    './_parameter',
    '../../../../../data/helper'
], function(Parameter, DataHelper) {
    return DataHelper.merged(Parameter, {
        fields: [
            {name: 'name', label: 'Field', type: 'label'}
        ]
    });
});
