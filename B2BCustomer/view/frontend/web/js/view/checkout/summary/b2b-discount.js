/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2022.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'BelVG_B2BCustomer/checkout/summary/b2b-discount'
            },
            totals: quote.getTotals(),

            isDisplayed: function() {
                return (this.getPureValue() !== '' && this.getPureValue() !== 0);
            },

            getTitle: function() {
                let title = '';
                if (this.totals() && totals.getSegment('b2b_discount')) {
                    title = totals.getSegment('b2b_discount').title;
                }
                return title;
            },

            getValue: function() {
                let price = 0;
                if (this.totals() && totals.getSegment('b2b_discount')) {
                    price = totals.getSegment('b2b_discount').value;
                }
                return '-' + this.getFormattedPrice(price);
            },

            /**
             *
             * @returns {string|number}
             */
            getPureValue: function() {
                let price = 0;
                if (this.totals() && totals.getSegment('b2b_discount')) {
                    price = totals.getSegment('b2b_discount').value;
                }
                return price;
            }
        });
    }
);
