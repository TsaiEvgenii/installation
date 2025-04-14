define(['jquery', 'uiRegistry'], function($,registry){
    'use strict';
    var widgetMixin = {
        firstRun: function firstRun(optionConfig, productConfig, base, self)
        {
            registry.get(['belvgSaleCountdownPercent', 'belvgB2BPercent'], function () {
                self.updateOldPriceManually();
            })
        },
    };

    return function(targetWidget){
        $.widget('belvg.oldPriceUpdater', targetWidget, widgetMixin);
        return $.belvg.oldPriceUpdater;
    };
});