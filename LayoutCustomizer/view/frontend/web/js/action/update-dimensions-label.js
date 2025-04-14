/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('belvg.updateDimensionLabel', {
        options: {
            width_selector: '.field[data-option-key="width"] .control input',
            height_selector: '.field[data-option-key="height"] .control input',
            width_unit_selector: '.dimensions-wrapper .width span.unit',
        },

        /** @inheritdoc */
        _create: function () {
            this.update();
            const widthInput = $(this.options.width_selector);
            const heightInput = $(this.options.height_selector);
            widthInput.on('change', this.update.bind(this))
            heightInput.on('change', this.update.bind(this))
        },

        update: function (){
            let labelText = '';
            const widthInput = $(this.options.width_selector);
            const heightInput = $(this.options.height_selector);
            const widthInputUnit = $(this.options.width_unit_selector);
            labelText = `${widthInput.val()} x ${heightInput.val()} ${widthInputUnit.text()}`;
            $(this.element).text(labelText);
        },

    });

    return $.belvg.updateDimensionLabel;
});
