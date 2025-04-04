/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
define(
    [
        'BelVG_AdditionalServices/js/view/checkout/summary/service',
    ],
    function (
        Component,
    ) {
        "use strict";

        return Component.extend({
            defaults: {
                totalCodeType: 'installation_service',
                template: 'BelVG_InstallationElements/checkout/summary/installation-service'
            },
        });
    }
);
