/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'BelVG_MadeInDenmark/js/model/product/price-difference-data',
], function (
    $,
    _,
    Component,
    priceDifferenceModel,
) {
    'use strict';

    return Component.extend({
        defaults: {
            isLoading: false,
        },

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            this._super()
                .observe(['isLoading']);

            return this;
        },

        isAllowed: function () {
            return true;
        },
    });
});
