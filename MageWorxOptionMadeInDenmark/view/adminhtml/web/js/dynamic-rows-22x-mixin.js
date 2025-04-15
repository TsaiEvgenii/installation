/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
define(function () {
    'use strict';

    let mixin = {
        defaults: {
            // made_in_denmark_price field was added
            notRequiredFields: ['price', 'qty', 'cost', 'weight', 'description', 'made_in_denmark_price']
        },
    };

    return function (dynamicRows22x) {
        return dynamicRows22x.extend(mixin);
    };
});