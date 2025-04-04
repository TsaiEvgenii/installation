/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

define([
    'mage/utils/wrapper',
    'jquery',
    'Magento_Customer/js/customer-data',
    'uiRegistry',
    'mage/translate',
    'BelVG_InstallationElements/js/model/popup',
], function (
    wrapper,
    $,
    customerData,
    registry,
    $t,
    installationPopup
) {
    'use strict';

    return function (proceedToCheckoutFunction) {
        return wrapper.wrap(proceedToCheckoutFunction, function (originalProceedToCheckoutFunction, config, element) {
            const checkInstallationProductConsistency = function (event) {
                const cart = customerData.get('cart');
                const placeOrderAllowed = cart()?.['belvg_installation_data']?.['is_place_order_allowed'] ?? false;
                if (!placeOrderAllowed) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    registry.get('installation-elements-panel.switcher.settings-popup.messages', function (installationPopupMessages) {
                        installationPopupMessages.removeAll();
                        let messageContainer = installationPopupMessages.messageContainer;
                        let message = $t('The number of items in the cart and for the installation product are different');
                        messageContainer.addErrorMessage({message: message});
                        installationPopup.showModal();
                    })
                }
            }

            $(element).on("click", checkInstallationProductConsistency);

            originalProceedToCheckoutFunction(config, element);
        });
    };
});
