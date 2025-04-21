/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (
    AbstractComponent
) {

    'use strict';
    return AbstractComponent.extend({
        defaults: {
            maxValue: 999,
        },

        initialize: function () {
            this._super();

            let self = this;
            this.value.subscribe(function(newVal) {
                if(self.decrButton) {
                    self.decrButton.disabled = newVal <= 1;
                }
                if(self.incrButton) {
                    self.incrButton.disabled = newVal >= self.maxValue;
                }
            });

            return this;
        },

        preventSymbols: function(data, event) {
            let txt = String.fromCharCode(event.which);
            return !!parseInt(txt) && !!txt.match(/[0-9]/);
        },

        maxLength: function() {
            if (this.value().length > 3)
                this.value(this.value().slice(0,3));
            if (/^0/.test(this.value())) {
                this.value(this.value().replace(/^0/, ""));
            }
        },

        increaseQty: function () {
            if(this.value() < this.maxValue) {
                this.value(parseInt(this.value()) + 1);
            }
        },

        decreaseQty: function () {
            if(this.value() > 1) {
                this.value(this.value() - 1);
            }
        },

        initDecrButton: function (elem) {
            this.decrButton = elem;
            elem.disabled = this.value() <= 1;
        },

        initIncrButton: function (elem) {
            this.incrButton = elem;
            elem.disabled = this.value() >= this.maxValue;
        }
    });
})
