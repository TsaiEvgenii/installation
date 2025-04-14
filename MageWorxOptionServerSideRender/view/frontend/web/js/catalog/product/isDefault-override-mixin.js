define(['jquery'], function($){
    'use strict';
    var widgetMixin = {
        afterFirstRun: function afterFirstRun(optionConfig, productConfig, base)
        {
            if (!this.options.is_default_enabled) {
                return;
            }

            if (this.options.router == 'admin_order_create') {
                return;
            }

            if (this.options.router != 'checkout') {
                //@todo renderSize optimization
                // this.processFirstRun(base);
            }
        },
        processFirstRun: function processFirstRun(base){
            $(document).trigger('processFirstRun-isDefault', base);
        }
    };

    return function(targetWidget){
        $.widget('mageworx.optionFeaturesIsDefault', targetWidget, widgetMixin);
        return $.mageworx.optionFeaturesIsDefault;
    };
});