/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'jquery',
    'uiRegistry',
    'mage/template',
    'priceUtils',
    'jquery-ui-modules/widget'
], function(
    $,
    registry,
    mageTemplate,
    utils
) {

    $.widget('belvg.priceQtyMultiplier', {
        options: {
            productPriceInfoSelector: '.product-info-price',
            productQtySelector: '[name="qty"]',
            priceTemplate: '<span class="price" attr-price="<%= data.value %>" ><%- data.formatted %></span>',
            priceClass: '.price',
            attrPrice: 'attr-price',
            afterInitPriceTimeout: 0,
            configurePage: false
        },
        customizerInit: false,
        priceInitialized: false,

        firstRun: function(optionConfig, productConfig, base, self) {
            // $(document).on('product-image-built', function (){
            //     self.customizerInit=true;
            // });
        },

        afterInitPrice: function(optionConfig, productConfig, base, self) {
            // var self = this;
            //
            // setTimeout(function () {
            //     self.applyChanges();
            // }, self.options.afterInitPriceTimeout);
            if (this.options.configurePage) {
                var self = this;

                setTimeout(function () {
                    self.applyChanges();
                }, self.options.afterInitPriceTimeout);
            }
        },


        applyChanges: function(base, productConfig) {
            var self = this;
            // if(self.customizerInit === false){
            //     return;
            // }

            var config = self.options,
                format = config.priceFormat,
                template = config.priceTemplate,
                qty_selector = $(config.productQtySelector),
                pc = $(config.productPriceInfoSelector).find('[data-price-type="finalPrice"]'),
                current_price = pc.find(config.priceClass).attr(config.attrPrice),
                qty = qty_selector.val(),
                qty_price = current_price * qty_selector.val(),
                templateData = {};


            template = mageTemplate(template);
            templateData.data = {
                value: qty_price,
                formatted: utils.formatPrice(qty_price, format)
            };
            pc.html(template(templateData));
        },

        update: function() {

        }
    });

    return $.belvg.priceQtyMultiplier;
});
