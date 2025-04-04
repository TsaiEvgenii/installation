/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'BelVG_InstallationElements/js/model/popup',
    'Magento_Customer/js/customer-data',
    'BelVG_InstallationElements/js/ko-binding/uncheck-confirmation'
], function (
    $,
    Component,
    quote,
    installationPopup,
    customerData
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BelVG_InstallationElements/installation-panel/switcher',
            checked: false,
            confirmationTitle: 'Confirmation title',
            confirmationContent: 'Confirmation content',
            listens: {
                'checked': 'onCheckedChanged',
            }
        },

        initialize: function () {
            let self = this;
            this._super();
            let cart = customerData.get('cart');
            cart.subscribe(function (updateCartData) {
                const checkedStatus = updateCartData?.['belvg_installation_data']?.['added_item_id'] ?? false;
                self.checked(Boolean(checkedStatus));
            });

            return this;
        },

        initObservable: function () {
            return this
                ._super()
                .observe('checked');
        },

        onCheckedChanged: function (newChecked) {
            let cart = customerData.get('cart');
            const installationProductAdded = Boolean(cart()?.['belvg_installation_data']?.['added_item_id'] ?? false);

            if (newChecked === true && !installationProductAdded) {
                installationPopup.showModal();
            } else if(newChecked === false && installationProductAdded) {
                // Find cart items to delete
                let itemsToDelete = [];
                const cartItems = cart().items || [];

                // Find IDs of both products
                cartItems.forEach(function (item) {
                    if (item.product_type === 'measurement_product_type' ||
                        item.product_type === 'installation_product_type'
                    ) {
                        itemsToDelete.push(item.item_id);
                    }
                });

                if (itemsToDelete.length) {
                    $('body').trigger('processStart');

                    // Function to delete an item by ID
                    const deleteItemById = function(itemId) {
                        return $.ajax({
                            url: BASE_URL + 'checkout/cart/delete',
                            type: 'POST',
                            data: {
                                id: itemId,
                                form_key: $.mage.cookies.get('form_key')
                            }
                        });
                    };

                    // Delete items sequentially
                    const deleteSequentially = async function() {
                        try {
                            for (const itemId of itemsToDelete) {
                                await deleteItemById(itemId);
                            }
                            // Update cart data
                            customerData.invalidate(['cart']);
                            customerData.reload(['cart'], true);

                            // Reload the page
                            window.location.reload();
                        } catch (error) {
                            console.error('Error removing items:', error);
                            $('body').trigger('processStop');
                        }
                    };

                    deleteSequentially();
                }
            }
        }
    });
});
