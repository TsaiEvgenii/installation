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
], function (customerData, storage) {
    'use strict';

    return function (cartId, storeId, params) {
        const customer = customerData.get('customer')();
        const isCustomerLoggedIn = Boolean(customer.firstname);
        const parameters = {
            'parameters': params
        }
        const serviceUrl = isCustomerLoggedIn
            ? 'rest/default/V1/carts/mine/quote-upgrader/upgrade'
            : `rest/default/V1/guest-carts/${cartId}/quote-upgrader/upgrade`;

        const payload = JSON.stringify({
            storeId,
            ...(isCustomerLoggedIn ? {cartId} : {}),
            ...parameters
        });

        return new Promise(function (resolve, reject) {
            storage.put(
                serviceUrl, payload, true, 'application/json', {}
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
