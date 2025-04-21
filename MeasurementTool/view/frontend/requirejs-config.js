var config = {
    'config': {
        'mixins': {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'BelVG_MeasurementTool/js/catalog-add-to-cart-mixin': true
            },
            'Magento_Checkout/js/proceed-to-checkout': {
                'BelVG_MeasurementTool/js/proceed-to-checkout-mixin': true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'BelVG_MeasurementTool/js/validation/validate-dynamic-rows-minimum-mixin': true
            },
            'Magento_Checkout/js/shopping-cart': {
                'BelVG_MeasurementTool/js/action/shopping-cart-mixin': true
            },
        }
    },
    map: {
        '*': {
            belvgCollapsibleSelect: 'BelVG_MeasurementTool/js/collapsible-select',
            'belvgCollapsibleRow': 'BelVG_MeasurementTool/js/collapsible-row-mobile',
            belvgRemoveMeasurementTool: 'BelVG_MeasurementTool/js/grid/remove-measurement-tool-confirmation'
        }
    }
};
