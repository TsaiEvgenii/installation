define([
    '../../../../../blocks/features/measurement-dependency',
    '../../../../../data/helper'
], function(MeasurementDependency, DataHelper) {
    return DataHelper.merged({}, {
        fields: [
            {name: 'params.breakPoint', label: 'Break Point', type: 'number'}
        ]
    });
});
