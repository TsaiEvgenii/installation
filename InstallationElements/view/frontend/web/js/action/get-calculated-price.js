/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2024.
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

    return function (payload) {
        const serviceUrl = '/rest/default/V1/installation/get-price';

        return new Promise(function (resolve, reject) {
            storage.post(
                serviceUrl, payload, false,'application/json', {}
            ).done(function (result) {
                resolve(result)
            }).fail(function (response) {
                reject(response)
            }).always(function () {
            });
        });
    };
});
