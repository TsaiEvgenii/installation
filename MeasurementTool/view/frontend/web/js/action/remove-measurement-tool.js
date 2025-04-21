/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'mage/storage',
], function (
    storage
) {
    'use strict';

    return function (measurementToolId) {
        const serviceUrl = `/rest/default/V1/measurement-tool/remove-measurement-tool/${measurementToolId}`;

        return new Promise(function (resolve, reject) {
            storage.delete(
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
