/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
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
                'title': $t('Calculate installation price'),
                'type': 'popup',
                'modalClass': 'installation-popup pos-bottom-right',
                'responsive': true,
                'closed': function (){
                    registry.get([
                        'installation-elements-panel.switcher',
                        'installation-elements-panel.price',
                    ], function (
                        switcherElement,
                        priceElement,
                    ) {
                        let cart = customerData.get('cart');
                        const installationProductAdded = Boolean(cart()?.['belvg_installation_data']?.['added_item_id'] ?? false);
                        if(!installationProductAdded){
                            switcherElement.checked(false);
                        }
                        priceElement.initPriceDataModel();
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
