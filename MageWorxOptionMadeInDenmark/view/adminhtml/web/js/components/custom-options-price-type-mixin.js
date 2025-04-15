/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

define([
    'underscore',
    'uiRegistry'
],function (_, uiRegistry) {
    'use strict';

    let mixin = {

        /**
         * {@inheritdoc}
         */
        onUpdate: function () {
            this._super();
            this.updateAddBeforeForMadeInDenmarkPrice();
        },
        /**
         * {@inheritdoc}
         */
        setInitialValue: function () {
            this._super();
            this.updateAddBeforeForMadeInDenmarkPrice();

            return this;
        },

        /**
         * Update addbefore for made in denmark price field. Change it to currency or % depends of price_type value.
         */
        updateAddBeforeForMadeInDenmarkPrice: function () {
            let addBefore, currentValue, madeInDenmarkPriceIndex, madeInDenmarkPriceName, uiPrice;

            madeInDenmarkPriceIndex = typeof this.imports.madeInDenmarkPriceIndex == 'undefined' ? 'made_in_denmark_price' : this.imports.madeInDenmarkPriceIndex;
            madeInDenmarkPriceName = this.parentName + '.' + madeInDenmarkPriceIndex;

            uiPrice = uiRegistry.get(madeInDenmarkPriceName);

            if (uiPrice && uiPrice.addbeforePool) {
                currentValue = this.value();

                uiPrice.addbeforePool.forEach(function (item) {
                    if (item.value === currentValue) {
                        addBefore = item.label;
                    }
                });

                if (typeof addBefore != 'undefined') {
                    uiPrice.addBefore(addBefore);
                }
            }
        }
    };

    return function (customOptionsPriceType) {
        return customOptionsPriceType.extend(mixin);
    };
});
