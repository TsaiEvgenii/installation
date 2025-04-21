/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'BelVG_MeasurementTool/js/action/get-customer-elements',
    'BelVG_MeasurementTool/js/action/remove-customer-element',
], function (
    $,
    _,
    Component,
    getCustomerElements,
    removeCustomerElement
) {
    'use strict';

    return Component.extend({
        defaults: {
            isLoading: false,
            elements: [],
            lastClickedElement: {},
            startNumberPosition: 1,
            tracks: {
                lastClickedElement: true
            },
            statefull: {
                lastClickedElement: true
            }

        },

        initialize: function () {
            this._super();
            this.initCustomerElements();
            this.initStartNumberPosition();
            return this;
        },

        initCustomerElements: function (){
            let self = this;
            getCustomerElements()
                .then(function (response) {
                    self.elements(response);
                    if (response.length > 0 && document.referrer.includes('measurement-tool')) {
                        document.getElementById('measurement-elements').scrollIntoView();
                    }
                })
                .catch(function (response) {
                    console.error(response);
                })
                .finally(function () {
                })
        },

        initStartNumberPosition: function () {
            const lastPositionElement = $('.product-item-count:last');
            if (lastPositionElement.length > 0) {
                this.startNumberPosition = parseInt(lastPositionElement.text()) + 1;
            }
        },

        removeElement: function(element){
            let self = this;
            this.isLoading(true);
            removeCustomerElement(element.entity_id)
                .then(function (response) {
                    if (response === true) {
                        let newElements = [];
                        self.elements().forEach((existingElement) => {
                            if (existingElement.entity_id !== element.entity_id) {
                                newElements.push(existingElement);
                            }
                        })
                        self.elements(newElements);
                        if(!newElements.length) {
                            $('#form-validate').shoppingCart('clearCart');
                        }
                    }
                })
                .catch(function (response) {
                    console.error(response);
                })
                .finally(function () {
                    self.isLoading(false);
                })
        },

        initObservable: function () {
            this._super()
                .observe(['isLoading', 'elements']);

            return this;
        },

        redirectElementType: function(element){
            const elementType = element.type;
            const linkToRedirect= window.measurementToolConfig?.['links']?.[elementType] ?? ''
            if (linkToRedirect !== '') {
                this.lastClickedElement = element;
                window.location.href = linkToRedirect;
            }
        },

        isAllowed: function () {
            return true;
        },
    });
});
