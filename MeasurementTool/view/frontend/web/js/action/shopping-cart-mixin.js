/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.shoppingCart', widget, {
            clearCart: function () {
                this._super();

                $('.measurement-elements .measurement-element BUTTON.delete-item').trigger('click');
            }
        });

        return $.mage.shoppingCart;
    }
});
