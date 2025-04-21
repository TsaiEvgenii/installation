/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

define([
    'jquery',
], function (
    $,
) {
    'use strict';

    return function (validator) {
        validator.addRule(
            'validate-dynamic-rows-minimum',
            function (rows, params, additionalParams) {
                return rows.length > 0;
            },
            $.mage.__('Please add at least one valid row.')
        );

        return validator;
    };
});
