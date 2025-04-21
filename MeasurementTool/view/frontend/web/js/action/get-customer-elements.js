/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2025.
 */

/**
 * @api
 */
define([
    'mage/storage',
], function (
    storage
) {
    'use strict';

    return function () {
        const serviceUrl = '/rest/default/V1/measurement-tool/get-customer-elements';

        return new Promise(function (resolve, reject) {
            storage.get(
                serviceUrl, true,'application/json', {}
            ).done(function (result) {
                resolve(result)
            }).fail(function (response) {
                reject(response)
            }).always(function () {
            });
        });
    };
});
