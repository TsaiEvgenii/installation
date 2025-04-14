define(['jquery', 'uiRegistry'], function($, registry){
    'use strict';
    var widgetMixin = {
        runUpdaters:function(self){
            registry.set('mageworxOptionBase', self);

            // Get existing updaters from registry
            var updaters = registry.get('mageworxOptionUpdaters');

            if (!updaters) {
                updaters = {};
            }
            var sortOrderArray = Object.keys(updaters).sort(function (a, b) {
                return a - b;
            });

            // Add each updater according to sort order
            $.each(sortOrderArray,function (key, value) {
                if (!updaters.hasOwnProperty(value)) {
                    return;
                }
                self.addUpdater(value, updaters[value]);
            });

            // Bind option change event listener
            self.addOptionChangeListeners();
            //display block with options by adding class with 'display: block;' prop
            self.element.find("#product-options-wrapper").addClass('show-options');
            registry.get('belvgLayoutConfig', function(layoutConfig) {
                self._layoutConfig = layoutConfig;
            });

            $(document).trigger('mageworxupdatersBuild');
        },

    };

    return function(targetWidget){
        $.widget('mageworx.optionBase', targetWidget, widgetMixin);
        return $.mageworx.optionBase;
    };
});