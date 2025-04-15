define([
    './dynamic-rows-unique',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'
], function(dynamicRowsUnique, validator, $t) {

    let ValidationRuleName = 'unique-material';

    validator.addRule(
        ValidationRuleName,
        function(value, params) {
            return params.isUnique;
        },
        $t('Material ID is not unique'));


    function getMaterialField(record) {
        return record.getChild('material-container')
            .getChild('material_id')
    }


    return dynamicRowsUnique.extend({
        getRecordKey: function(record) {
            let field = getMaterialField(record);
            return field ? field.value() : null;
        },

        markNonUniqueRecord: function(record) {
            let field = getMaterialField(record);
            field.setValidation(ValidationRuleName, {
                isUnique: false
            });
        },

        unmarkNonUniqueRecord: function(record) {
            let field = getMaterialField(record);
            field.setValidation(ValidationRuleName, {
                isUnique: true
            });
        }
    });
});
