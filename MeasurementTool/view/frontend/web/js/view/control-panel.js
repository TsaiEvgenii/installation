define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'Magento_Customer/js/customer-data',
], function (
    $,
    Component,
    registry,
    customerData,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BelVG_MeasurementTool/control-panel',
            visible: false
        },

        initialize: function () {
            this._super()
                .addToCartListener()
                .initControlPanel();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe(['visible']);

            return this;
        },

        initControlPanel: function () {
            const localStorage = registry.get("localStorage");
            const measurementElement = localStorage.get('measurement-elements.lastClickedElement');
            if (measurementElement) {
                const customer = customerData.get('customer');
                if (parseInt(customer().id) === parseInt(measurementElement.customer_id)) {
                    this.visible(true);
                }
            }
        },

        removeMeasurementToolElementData: function () {
            const localStorage = registry.get("localStorage");
            localStorage.remove('measurement-elements.lastClickedElement');
            this.visible(false);
        },

        addToCartListener: function (){
            let self = this;
            $(document).on('ajax:addToCart', function (event, resObject) {
                const localStorage = registry.get("localStorage");
                const measurementElement = localStorage.get('measurement-elements.lastClickedElement');
                self.visible(!!measurementElement);
            });

            return this;
        }
    });
});
