/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2025.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
], function (
    $,
    _,
    Component,
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
