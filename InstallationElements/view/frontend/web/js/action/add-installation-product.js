/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2024.
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

    return function (cartId, storeId, installationData) {
        let serviceUrl;
        let customer = customerData.get('customer');
        let data = {
            storeId: storeId,
            installation_data: installationData
        };

        if (customer().firstname) {
            serviceUrl = 'rest/default/V1/carts/mine/installation/add-product';
            data['cartId'] = cartId;
        } else {
            serviceUrl = `rest/default/V1/guest-carts/${cartId}/installation/add-product`;
        }

        const payload = JSON.stringify(data);

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
