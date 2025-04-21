/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/record'
], function ($, Record) {
    'use strict';

    return Record.extend({
        defaults: {
            rowTitleTemplate: 'BelVG_MeasurementTool/dynamic-rows/row-title',
        },
    });
});
