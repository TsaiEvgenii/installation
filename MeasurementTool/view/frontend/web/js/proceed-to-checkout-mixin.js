define([
    'mage/utils/wrapper',
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
], function (
    wrapper,
    $,
    confirmation,
    customerData
) {
    'use strict';

    function isCustomer() {
        const customer = customerData.get('customer');
        return customer().firstname;
    }

    return function (proceedToCheckoutFunction) {
        let isProcessing = false;
        return wrapper.wrap(proceedToCheckoutFunction, function (originalProceedToCheckoutFunction, config, element) {
            element.addEventListener('click', function (event) {
                if (isProcessing) {
                    return;
                }
                const measurementElements = $('.measurement-element');
                if (measurementElements.length > 0 && isCustomer()) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    isProcessing = true;
                    const eventData = {
                        type: event.type,
                        target: event.target,
                        currentTarget: event.currentTarget,
                        originalEvent: event
                    };
                    confirmation({
                        modalClass: 'info-confirmation',
                        title: $.mage.__('You have %1 measurement elements to customize').replace('%1', measurementElements.length),
                        content: $.mage.__('Note that you have measurements that are not adjusted. Press "X" if you want to adjust the remaining measurements before continuing to the curve.'),
                        buttons: [{
                            text: $.mage.__('Customize your final measurements'),
                            class: 'action primary',
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $.mage.__('Continue your purchase without final adjustments'),
                            class: 'link',
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }],
                        actions: {
                            confirm: function(){
                                const newEvent = $.Event(eventData.type, {
                                    originalEvent: eventData.originalEvent,
                                    target: eventData.target,
                                    currentTarget: eventData.currentTarget
                                });

                                $(eventData.target).trigger(newEvent);
                            },
                            cancel: function(){
                                isProcessing = false;
                            },
                            always: function(){}
                        }
                    });
                }
            }, true);

            originalProceedToCheckoutFunction(config, element);
        });
    };
});
