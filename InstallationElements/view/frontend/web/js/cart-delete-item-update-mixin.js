/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

define([
    'jquery',
    'mage/url',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm'
], function ($, url, totals, customerData, confirm) {
    'use strict';

    return function (widget) {
        $.widget('belvg.deleteCartItemAjax', widget, {
            options: {
                url: {
                    'removeMultiple': url.build('checkout/cart/deleteMultiple')
                },
                confirmRelatedMessage: $.mage.__('You are about to delete a measurement product. The related installation product will also be removed. Do you want to continue?'),
                measurementProductType: 'measurement_product_type',
                installationProductType: 'installation_product_type'
            },

            /** @inheritdoc */
            _bind: function () {
                let self = this,
                    events = {};

                events['click'] = function (event) {
                    event.stopPropagation();

                    // Get cart data
                    const cart = customerData.get('cart')();
                    if (!cart || !cart.items || !cart.items.length) {
                        self._showStandardConfirmation(event);
                        return;
                    }

                    // Get current item being deleted
                    const cartItemId = $(event.currentTarget).data('cart-item');
                    const currentItem = cart.items.find(item => item.item_id == cartItemId);

                    // Check if current item is measurement_product_type
                    if (currentItem && currentItem.product_type === self.options.measurementProductType) {
                        // Find if there's an installation_product_type in the cart
                        const installationItem = cart.items.find(item =>
                            item.product_type === self.options.installationProductType
                        );

                        if (installationItem) {
                            // Show special confirmation for related products
                            self._showRelatedProductsConfirmation(event, currentItem, installationItem);
                            return;
                        }
                    }

                    // Standard confirmation for regular products
                    self._showStandardConfirmation(event);
                };

                this._on(this.element, events);
            },

            /**
             * Show standard confirmation dialog
             *
             * @param {Event} event
             * @private
             */
            _showStandardConfirmation: function(event) {
                let self = this;

                confirm({
                    content: self.options.confirmMessage,
                    modalClass: 'cart-delete-item-update fit-content',
                    actions: {
                        /** @inheritdoc */
                        confirm: function () {
                            self._removeItem($(event.currentTarget));
                        },

                        /** @inheritdoc */
                        always: function (e) {
                            e.stopImmediatePropagation();
                        }
                    }
                });
            },

            /**
             * Show confirmation dialog for related products
             *
             * @param {Event} event
             * @param {Object} measurementItem
             * @param {Object} installationItem
             * @private
             */
            _showRelatedProductsConfirmation: function(event, measurementItem, installationItem) {
                let self = this;

                confirm({
                    title: $.mage.__('Delete Confirmation'),
                    content: self.options.confirmRelatedMessage,
                    modalClass: 'cart-delete-item-update fit-content',
                    actions: {
                        confirm: function () {
                            self._removeMultipleItems(measurementItem.item_id, installationItem.item_id);
                        },
                        cancel: function () {
                            return false;
                        }
                    },
                    buttons: [{
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }, {
                        text: $.mage.__('Delete Both Items'),
                        class: 'action-primary action-accept',
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    }]
                });
            },

            /**
             * Remove multiple items from cart
             *
             * @param {Number} measurementItemId
             * @param {Number} installationItemId
             * @private
             */
            _removeMultipleItems: function (measurementItemId, installationItemId) {
                let self = this;
                let items = [measurementItemId, installationItemId];

                self._ajax(
                    self.options.url.removeMultiple,
                    { items: items },
                    null,
                    function(elem, response) {
                        self._removeMultipleItemsAfter(items, response);
                    }
                );
            },

            /**
             * Update content after multiple items are removed
             *
             * @param {Array} itemIds Array of item IDs that were removed
             * @param {Object} response Response from the server
             * @private
             */
            _removeMultipleItemsAfter: function(itemIds, response) {
                if (response.success && response.removed_items && response.removed_items.length) {
                    let listOfItems = $('li[cart-item-identifier], tr[cart-item-identifier]');
                    let counter = 1;
                    let removedNames = [];

                    // If only these items are in cart, reload
                    if (listOfItems.length <= response.removed_items.length) {
                        window.location.reload();
                        return;
                    }

                    // Remove items from the DOM
                    $.each(response.removed_items, function(index, itemId) {
                        let itemElement = $('[cart-item-identifier=' + itemId + ']');
                        if (itemElement.length) {
                            let productName = itemElement.find('.product-desc-name a, .product-item-name a').text().trim();
                            if (productName) {
                                removedNames.push(productName);
                            }
                            itemElement.remove();
                        }
                    });

                    // Update counters for remaining items
                    listOfItems.each(function() {
                        let itemId = $(this).attr('cart-item-identifier');
                        if (!response.removed_items.includes(parseInt(itemId))) {
                            $(this).find('.count').text(counter);
                            counter++;
                        }
                    });

                    // Show success message
                    if (removedNames.length) {
                        let message = removedNames.length === 1
                            ? $.mage.__('Cart item %1 was successfully removed').replace('%1', removedNames[0])
                            : $.mage.__('Multiple items were successfully removed from your cart');

                        customerData.set('messages', {
                            messages: [{
                                type: 'success',
                                text: message
                            }]
                        });
                    }

                    // Reload the mini cart
                    customerData.reload(['cart'], true);

                    // Reload the totals summary block
                    let deferred = $.Deferred();
                    totals([], deferred);

                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                }
            },

            /**
             * Make AJAX request
             *
             * @param {String} url
             * @param {Object} data
             * @param {jQuery} elem
             * @param {Function} callback
             * @private
             */
            _ajax: function (url, data, elem, callback) {
                $.extend(data, {
                    'form_key': $.mage.cookies.get('form_key')
                });

                $.ajax({
                    url: url,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        $(document.body).trigger('processStart');
                    },
                    complete: function (res) {
                        $(document.body).trigger('processStop');

                        $(document).trigger('ajax:cartDeleteItem', {
                            'request': data,
                            'response': res
                        });
                    },
                    success: function (response) {
                        let msg;

                        if (response.success) {
                            callback.call(this, elem, response);
                        } else {
                            msg = response.error_message;
                            if (msg) {
                                self._showErrorMessage(msg);
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error performing request:', error);
                    }
                });
            },

            /**
             * Show error message
             *
             * @param {String} message
             * @private
             */
            _showErrorMessage: function(message) {
                require(['Magento_Ui/js/modal/alert'], function(alert) {
                    alert({
                        content: message
                    });
                });
            }
        });

        return $.belvg.deleteCartItemAjax;
    };
});