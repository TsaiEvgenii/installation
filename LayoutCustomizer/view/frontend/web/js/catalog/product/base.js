
define([
    'jquery',
    'priceUtils',
    'uiRegistry',
    'underscore'
], function ($, utils, registry, _) {
    'use strict';

    $.belvgLayoutBase = function() {return {
        init: function (options) {
            var settings = $.extend({
                cm_in_sqm: 10000,
                sale_percent: 0
            }, options);

            this.config = settings;

            return this;
        },

        getConfig: function () {
            return this.config;
        },

        getOverallWidth: function () {
            var self = this,
                config = this.config;

            return parseFloat($('[mageworx_option_id='+config.overall_width+'] input').val());
        },

        getOverallHeight: function () {
            var self = this,
                config = this.config;

            return parseFloat($('[mageworx_option_id='+config.overall_height+'] input').val());
        },

        getSquare: function () {
            var self = this,
                config = this.config;

            return parseFloat(self.getOverallWidth() * self.getOverallHeight()) / config.cm_in_sqm;
        },

        getSalePercent: function () {
            return this.config.sale_percent;
        },

        /**
         * @todo: handle taxes here
         *
         * In case of change please sync changes with:
         * `BelVG\LayoutCustomizer\Model\ResourceModel\Layout::getLayoutData()` method
         *
         * @returns {number}
         */
        getBaseLayoutPrice: function () {
            if (typeof this.config == 'undefined' || this.config.layout_props == 'undefined') {
                console.log('Please check layout, most likely this product does not have assigned layout');
                return 0;
            }

            var self = this,
                config = this.config,
                layout_props = this.config.layout_props,
                square = this.getSquare(),
                base_price = 0,
                sqm_price_step1 = parseFloat(layout_props.sqm_price),
                sqm_price_step2 = layout_props.sqm_level_step2 > 0 ?
                    parseFloat(layout_props.sqm_price) + parseFloat(layout_props.sqm_price_step2) :
                    parseFloat(layout_props.sqm_price),
                sqm_price = layout_props.sqm_level_step2 > 0 && square > layout_props.sqm_level_step2 ?
                    parseFloat(sqm_price_step2) :
                    parseFloat(sqm_price_step1);

            base_price = self.getOverallWidth() * parseFloat(layout_props.horizontal_frame) +
                self.getOverallHeight() * parseFloat(layout_props.vertical_frame) +
                parseFloat(layout_props.base_price) +
                sqm_price * parseFloat(square);

            return parseFloat(base_price);
        }
    };};

    return $.belvgLayoutBase();
});
