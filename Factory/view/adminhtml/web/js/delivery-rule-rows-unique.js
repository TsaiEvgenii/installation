define([
    './dynamic-rows-unique',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'
], function(dynamicRowsUnique, validator, $t) {

    let ValidationRuleName = 'unique-delivery-rule';

    validator.addRule(
        ValidationRuleName,
        function(value, params) {
            return params.isUnique
        },
        $t('Category/Colors pair is not unique'));


    function getCategoryField(record) {
        return record.getChild('category_id');
    }

    function getColorsField(record) {
        return record.getChild('colors');
    }

    function getFields(record) {
        return [getCategoryField(record), getColorsField(record)];
    }


    return dynamicRowsUnique.extend({
        getRecordKey: function(record) {
            let categoryField = getCategoryField(record),
                colorsField = getColorsField(record);
            if (!categoryField || !colorsField)
                return null;

            return [categoryField, colorsField]
                .map(function(field) { return '' + field.value(); })
                .join('--');
        },

        markNonUniqueRecord: function(record) {
            getFields(record).forEach(function(field) {
                field.setValidation(ValidationRuleName, {
                    isUnique: false
                });
            });
        },

        unmarkNonUniqueRecord: function(record) {
            getFields(record).forEach(function(field) {
                field.setValidation(ValidationRuleName, {
                    isUnique: true
                });
            });
        }
    });
});
