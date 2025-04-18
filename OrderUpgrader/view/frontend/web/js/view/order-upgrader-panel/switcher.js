/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2025.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'BelVG_OrderUpgrader/js/model/popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm'
], function (
    $,
    Component,
    quote,
    orderUpgraderPopup,
    customerData,
    confirmation
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BelVG_OrderUpgrader/order-upgrader-panel/switcher',
            checked: false,
            confirmationEnabled: false,
            confirmationTitle: 'Confirmation title',
            confirmationContent: 'Confirmation content',
            listens: {
                'checked': 'onCheckedChanged',
            }
        },

        initialize: function () {
            let self = this;
            this._super();

            return this;
        },

        initObservable: function () {
            return this
                ._super()
                .observe('checked');
        },
        onUpdate: function (value) {
        },

        onCheckedChanged: function (newChecked) {
            if (newChecked && !this.confirmationEnabled) {
                orderUpgraderPopup.showModal();
                return;
            }
            let self = this;

            if (newChecked) {
                confirmation({
                    title: self.confirmationTitle || '',
                    content: self.confirmationContent || '',
                    actions: {
                        confirm: function () {
                            orderUpgraderPopup.showModal();
                        },
                        cancel: function () {
                            self.checked(false);
                        },
                        always: function () {
                        }
                    }
                })
            }
        },
    });
});
