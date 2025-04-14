/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
        'uiRegistry',
        'Magento_Customer/js/customer-data',
        'jquery'
    ],
    function (
        registry,
        customerData,
        $
    ) {
        'use strict';

        var mixin = {
            initCountdown: function () {
                var self = this,
                    dynamicRuleProduct = registry.get('belvgDynamicRuleProduct');
                let b2bDiscount = customerData.get('b2b_discount'),
                    b2bDiscountData = b2bDiscount();

                if (dynamicRuleProduct || (b2bDiscountData.b2b_discount ?? false)) {
                    if (dynamicRuleProduct) {
                        var countdownData = customerData.get('belvg_salecountdown'),
                            countdownDataValue = countdownData();

                        // set percent value based on DynamicRule
                        countdownDataValue.percent = dynamicRuleProduct.percent;
                        // server side render optimization
                        // self.update(countdownDataValue);

                        countdownData.subscribe(function (updatedCountdown) {
                            // set percent value based on DynamicRule
                            updatedCountdown.percent = dynamicRuleProduct.percent;

                            self.update(updatedCountdown);
                        }, this);
                        customerData.reload(['belvg_salecountdown'], false);
                    } else {
                        registry.set('belvgSaleCountdownPercent', {empty: true});
                    }

                    if (b2bDiscountData.b2b_discount ?? false) {
                        registry.set('belvgB2BPercent', b2bDiscountData.b2b_discount);
                    } else {
                        registry.set('belvgB2BPercent', {empty: true});
                    }


                    return;
                }



                return self._super();
            },
            update: function (response) {
                let self = this;

                if (response.date == null) {
                    this.showPrice();
                    return;
                }

                self.percent(response.percent);
                self.countdownText(response.countdownText);

                registry.set('belvgSaleCountdownPercent', response.percent);
                $('#' + this.elementId).addClass('countdown-visible');
                $('#' + self.elementId).countdown(Date.now() + response.diff).on('update.countdown', function (event) {
                    self.updateTime(event);
                });
                this.showPrice();
            },
        };

        return function (target) {
            return target.extend(mixin);
        };
    });
