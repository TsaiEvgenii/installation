/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2025.
 */
define([
    'jquery',
    'uiRegistry',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function (
    $,
    registry,
    customerData,
    modal,
    $t
) {
    'use strict';

    return {
        modalWindow: null,

        createPopUp: function (element) {
            this.modalWindow = element;
            const options = {
                'title': $t('Compare prices or choose another material'),
                'type': 'popup',
                'modalClass': 'order-upgrader-popup pos-bottom-right',
                'responsive': true,
                'closed': function (){
                    registry.get([
                        'order-upgrader-elements-panel.switcher',
                    ], function (
                        switcherElement,
                    ) {
                        switcherElement.checked(false);
                    });
                },
                'buttons': []
            };
            modal(options, $(this.modalWindow));
        },

        showModal: function () {
            $(this.modalWindow).modal('openModal');
        },

        closeModal: function () {
            $(this.modalWindow).modal('closeModal');
        },

        clickAction: function () {
            this.showModal();
        }
    }
});
