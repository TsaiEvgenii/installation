define([
    'jquery',
    'mage/translate',
    'Magento_Catalog/product/view/validation'
], function($) {
    'use strict';

    $.validator.addMethod(
        'validate-disabled-selected-option',
        function(value, element) {
            return false;
        },
        $.mage.__('Unavailable option selected')
    );
});
