/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2024.
 */

define(['jquery'], function($) {

    return function(priceOptionsWidget) {
        $.widget('mage.priceOptions', priceOptionsWidget, {
            _init: function () {
                // do not trigger product options change
                // [https://app.asana.com/0/1202243638585273/1208466312693738/f]
                // this._super();
            }
        });

        return $.mage.priceOptions;
    }
});
