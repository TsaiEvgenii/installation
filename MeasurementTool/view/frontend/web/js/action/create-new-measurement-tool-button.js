/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

define([
    'jquery',
    'BelVG_CustomerLoginPopup/js/model/authentication-popup',
    'BelVG_CustomerLoginPopup/js/action/login',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
    'jquery-ui-modules/widget',
    'jquery/ui'
], function (
    $,
    ajaxLoginPopup,
    loginAction,
    customerData,
    registry
) {
    'use strict';

    $.widget('belvg_measurement_tool.button', $.ui.button, {
        options: {
            measurementToolPageUrl: ''
        },

        /**
         * Button creation.
         * @protected
         */
        _create: function () {
            this._bind();
            this._super();
        },

        /**
         * Bind handler on button click.
         * @protected
         */
        _bind: function () {
            this.element
                .off('click.button')
                .on('click.button', $.proxy(this._click, this));
        },

        /**
         * Button click handler.
         * @protected
         */
        _click: function () {
            const localStorage = registry.get("localStorage");
            let self = this;
            if (this.isCustomer()) {
                localStorage.remove('measurement-elements.lastClickedElement')
                window.location.href = this.options.measurementToolPageUrl;
            } else {
                loginAction.registerLoginCallback(function () {
                    localStorage.remove('measurement-elements.lastClickedElement')
                    setTimeout(function () {
                        window.location.href = self.options.measurementToolPageUrl;
                    }, 0);
                });
                ajaxLoginPopup.showModal();
            }
        },

        isCustomer: function () {
            const customer = customerData.get('customer');
            return customer().firstname;
        },
    });

    return $.belvg_measurement_tool.button;
});
