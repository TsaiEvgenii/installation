var config = {
    map: {
        '*': {
            layoutBase: 'BelVG_LayoutCustomizer/js/catalog/product/base',
            optionFeatures: 'BelVG_LayoutCustomizer/js/catalog/product/option-features-override',
            productPageLoader: 'BelVG_LayoutCustomizer/js/catalog/product/view/product-page-loader',
            optionsAccordion: 'BelVG_LayoutCustomizer/js/jquery-ui-module/product-option-accordion'
        }
    },
    config: {
        mixins: {
            "mage/validation": {
                "BelVG_LayoutCustomizer/js/catalog/product/mage-validation-mixin": true
            },
            'Magento_Catalog/js/price-options': {
                'BelVG_LayoutCustomizer/js/catalog/product/price-options-mixin': true
            }
        }
    }
};
