var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/proceed-to-checkout': {
                'BelVG_InstallationElements/js/proceed-to-checkout-mixin': true
            },
            'Magento_Checkout/js/view/cart-item-renderer': {
                'BelVG_InstallationElements/js/view/cart-item-renderer-mixin': true
            },
            'BelVG_UpdateCartAjax/js/cart-delete-item-update': {
                'BelVG_InstallationElements/js/cart-delete-item-update-mixin': true
            }
        }
    }
};
