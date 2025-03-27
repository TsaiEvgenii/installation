/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */

define([
    'ko',
    'Magento_Ui/js/modal/confirm'
], function (
    ko,
    confirmation
) {
    'use strict'

    ko.bindingHandlers.uncheckConfirmation = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            element.addEventListener('mousedown', function (e) {
                if (element.checked) {
                    confirmation({
                        title: allBindings()?.confirmationTitle || '',
                        content: allBindings()?.confirmationContent || '',
                        actions: {
                            confirm: function () {
                                element.checked = !element.checked;
                                valueAccessor()(element.checked);
                            },
                            cancel: function () {
                            },
                            always: function () {
                            }
                        }
                    })
                } else {
                    element.checked = !element.checked;
                    valueAccessor()(element.checked);
                }
            });
            element.checked = valueAccessor()();
        }
    }
})
