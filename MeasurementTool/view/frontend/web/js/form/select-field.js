/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'ko',
    'Magento_Ui/js/form/element/select',
    'jquery',
], function (
    ko,
    SelectComponent,
    $
) {

    'use strict';
    return SelectComponent.extend({
        defaults: {
            valueLabel: ''
        },

        initialize: function () {
            this._super();

            let self = this;

            if(this.value()) {
                this.initSelectLabel(this.value());
            }

            this.value.subscribe(function(newVal) {
                self.initSelectLabel(newVal);
            });

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(['valueLabel']);

            return this;
        },

        initSelectLabel: function (value) {
            this.valueLabel(this.getChosenLabel(value));
        },

        getChosenLabel: function (value) {
            let chosenOption = this.getOption(value);
            return chosenOption ? chosenOption.label : '';
        },

        choseOption: function (option) {
            this.value(option.value);
        },
    });
})
