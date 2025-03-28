define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.cart = customerData.get('cart');
        },

        isEnabled: function () {
            return this.cart().belvg_minicart.b2b_discount_data;
        },

        getTitle: function () {
            return this.cart().belvg_minicart.b2b_discount_data.title;
        },

        getValue: function () {
            return this.cart().belvg_minicart.b2b_discount_data.value;
        },

    });
});
