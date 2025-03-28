/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'BelVG_SaveCartTotals/js/view/summary/abstract-total',
    'BelVG_SaveCartTotals/js/model/quote'
], function(AbstractTotal, quote) {
    'use strict';

    let config = window.cartConfig;

    return AbstractTotal.extend({
        getPureValue: function() {
            let totals = quote.getTotals()();
            return totals ? totals.b2b_discount : quote.b2b_discount;
        },

        isAllowed: function() {
            let total = this.getPureValue();

            if (total > 0 || total < 0) {
                return true
            }

            return false;
        },

        getValue: function () {
            return '-' + this.getFormattedPrice(this.getPureValue());
        }
    })
});
