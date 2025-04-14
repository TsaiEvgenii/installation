var config = {
    map: {
        '*': {
           'BelVG_SalesDynamicRule/js/timer/belvg_timer-mixin':
               'BelVG_MageWorxOptionServerSideRender/js/timer/belvg_timer-mixin-override',
           'BelVG_MageWorxSpecialColor/js/catalog/product/url-management-mixin':
               'BelVG_MageWorxOptionServerSideRender/js/catalog/product/url-managment-mixin'
        }
    },
    config: {
        mixins: {
            'BelVG_MageWorxUrls/js/catalog/product/isDefault-override': {
                'BelVG_MageWorxOptionServerSideRender/js/catalog/product/isDefault-override-mixin':true
            },
            'BelVG_LayoutOptionPriceType/js/catalog/product/base-override': {
                'BelVG_MageWorxOptionServerSideRender/js/catalog/product/base-override-mixin': true
            },
            'BelVG_SaleCountdown/js/old_price_updater': {
                'BelVG_MageWorxOptionServerSideRender/js/catalog/product/old_price_updater-mixin': true
            },
            'BelVG_LayoutCustomizer/js/catalog/product/option-features-override': {
                'BelVG_MageWorxOptionServerSideRender/js/catalog/product/option-features-override-mixin': true
            },
            'BelVG_MageWorxOptionFeatures/js/swatches/additional-override': {
                'BelVG_MageWorxOptionServerSideRender/js/swatches/additional-override-mixin': true
            }
        }
    }
};







