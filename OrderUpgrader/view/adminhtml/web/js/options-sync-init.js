define([
    'jquery',
    'BelVG_OrderUpgrader/js/options-sync',
    'domReady!'
], function ($, optionsSync) {
    'use strict';

    return function() {
        // Initialize options sync
        $(function() {
            // Check if we're on the right page with interval
            let checkInterval = setInterval(function() {
                if ($('#belvg_order_upgrader_options_config_types').length > 0 &&
                    $('#belvg_order_upgrader_options_config_values').length > 0) {
                    clearInterval(checkInterval);

                    // Initialize sync with correct selectors
                    optionsSync({
                        typesFieldSelector: '#belvg_order_upgrader_options_config_types',
                        valuesFieldSelector: '#belvg_order_upgrader_options_config_values',
                        optionCodeSelector: 'select[id$="_option_code"]'
                    });
                }
            }, 500);

            // Clear interval after 10 seconds to prevent memory leaks
            setTimeout(function() {
                clearInterval(checkInterval);
            }, 10000);
        });
    };
});