/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'jquery',
    'uiRegistry',
    'layoutBase',
    'Magento_Catalog/js/price-utils',
    'jquery-ui-modules/widget'
], function($, registry, layoutBase, priceUtils) {
    let _layoutBase = layoutBase;

    $.widget('belvg.sqmPriceUpdater', {
        options: {
            choiceInputs: '.product-options-wrapper .choice',
            currentPriceInput: '.price.current span.price',
            oldPriceWrapper: '.old-option-price span.price',
            currentPriceLabelSelector: 'label .title-cont .price'
        },

        _layoutConfig: null,

        _create: function() {
            registry.get('belvgLayoutConfig', this._initLayoutConfig.bind(this));
        },

        _initLayoutConfig: function(layoutConfig) {
            this._layoutConfig = layoutConfig;
            this._updateOptions();
        },

        firstRun: function(optionConfig, productConfig, base, self) {

        },

        afterInitPrice: function(optionConfig, productConfig, base, self) {

        },

        applyChanges: function(base, productConfig) {
            this._updateOptions();
        },

        update: function() {

        },

        _updateOptions: function() {
            let self = this,
                square = parseFloat(_layoutBase.getSquare()),
                layoutPrice = _layoutBase.getBaseLayoutPrice(),
                saleRulePercent = _layoutBase.getSalePercent();

            if (!this._layoutConfig || isNaN(square) || square <= 0) {
                return;
            }

            $(self.options.choiceInputs).each(function() {
                let input = $(this),
                    pricePerSqm = $(input).attr('sqmprice'),
                    curPriceInput = $(input).find(self.options.currentPriceInput),
                    oldPriceInput = $(input).find(self.options.oldPriceWrapper),
                    percentMultiplier = (100-saleRulePercent)/100,
                    oldPrice = pricePerSqm * square,
                    curPrice = pricePerSqm * square * percentMultiplier;

                if (pricePerSqm > 0 && layoutPrice > 0) {
                    curPriceInput.text(priceUtils.formatPrice(curPrice));
                    oldPriceInput.text(priceUtils.formatPrice(oldPrice));

                    var isActive = $(input).hasClass('active');
                    if (isActive) {
                        var label = $(input)
                            .parents('.field').first()
                            .find(self.options.currentPriceLabelSelector).first(); //find label to update it's price
                        label.text(priceUtils.formatPrice(curPrice));
                    }
                }
            })
        }
    });

    return $.belvg.sqmPriceUpdater;
});
