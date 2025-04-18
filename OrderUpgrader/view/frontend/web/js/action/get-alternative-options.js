/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2025.
 */

/**
 * @api
 */
define([
    'Magento_Customer/js/customer-data',
    'mage/storage',
], function (
    customerData,
    storage,
) {
    'use strict';

    return function (cartId) {
        let serviceUrl;
        let customer = customerData.get('customer');
        if (customer().firstname) {
            serviceUrl = '/rest/default/V1/carts/mine/order-upgrader/get-alternative-options';
        } else {
            serviceUrl = `/rest/default/V1/guest-carts/${cartId}/order-upgrader/get-alternative-options`;
        }

        return new Promise(function (resolve, reject) {
            storage.get(
                serviceUrl, true, 'application/json', {}
            ).fail(
                function (response) {
                    reject(response)
                }
            ).done(
                function (response) {
                    resolve(response)
                }
            ).always(
                function () {
                }
            );
        });
    };
});
