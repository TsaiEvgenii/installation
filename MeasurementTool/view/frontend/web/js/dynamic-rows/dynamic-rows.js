/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'Magento_Ui/js/lib/validation/validator',
    'matchMedia',
    'belvgCollapsibleRow'
], function ($, DynamicRows, validator, mediaCheck) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            error: '',
            listens: {
                '${ $.provider }:${ $.customScope ? $.customScope + "." : ""}data.validate': 'validate',
            },
        },

        initObservable: function () {
            var rules = this.validation = this.validation || {};

            this._super();
            this.observe('error');

            return this;
        },

        validate: function () {
            let rows = this.elems();

            let result = validator(this.validation, rows, this.validationParams),
                message = !this.disabled() && this.visible() ? result.message : '',
                isValid = this.disabled() || !this.visible() || result.passed;

            this.error(message);
            this.error.valueHasMutated();
            this.bubble('error', message);

            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        },

        processingAddChild: function (ctx, index, prop) {
            this._super(ctx, index, prop);
            this.error('');
            this.error.valueHasMutated();
            this.bubble('error', message);
        },

        initCollapsible: function (element) {
            mediaCheck({
                media: '(min-width: 1024px)',
                entry: function () {
                    if($(element).data('collapsible')) {
                        $(element).collapsibleRow("activate");
                        $(element).collapsibleRow("destroy");
                    }
                },
                exit: function () {
                    console.log('leaving 1024');
                    $(element).collapsibleRow({
                        collapsible: true,
                        active: false,
                        option: false,
                        heightStyle: "content",
                    })
                }
            });
        }
    });
});
