/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2024.
 */

define([], function () {

    return function (cartItemRendererComponent) {
        return cartItemRendererComponent.extend({
            defaults: {
                productTypeExclQty: ["installation_product_type"]
            },

            showQty: function (item) {
                return !this.productTypeExclQty.includes(item.product_type);
            }
        })
    }

})
