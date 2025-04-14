define([
    '../../data/helper',
    '../../object/types',
    '../widgets/dialogs/feature-select',
    '../widgets/dialogs/measurement-select',
    '../widgets/dialogs/parameter-name-select',
    '../widgets/dialogs/link-name-select'
], function(
    DataHelper,
    ObjectTypes,
    FeatureSelectDialog,
    MeasurementSelectDialog,
    ParameterNameSelectDialog,
    LinkNameSelectDialog) {

    return DataHelper.merged(ObjectTypes, {
        feature: {
            subtypeSelectDialog: FeatureSelectDialog.Widget
        },
        measurement: {
            subtypeSelectDialog: MeasurementSelectDialog.Widget
        },
        parameter: {
            initDataDialog: ParameterNameSelectDialog.Widget
        },
        link: {
            initDataDialog: LinkNameSelectDialog.Widget
        }
    });
});
