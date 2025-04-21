/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'jquery',
    'mage/translate',
    'underscore',
    'uiRegistry',
    'Magento_Customer/js/customer-data',
    'jquery-ui-modules/widget'
], function (
    $,
    $t,
    _,
    registry,
    customerData
) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', $.mage.catalogAddToCart, {
            _create: function () {
                this._super();
                if (this.isCustomer()) {
                    this._createMeasurementToolHiddenField();
                    this._removeMeasurementToolElementData();
                }
            },

            _bindSubmit: function () {
                if(this.isCustomer()){
                    this._setMeasurementToolElementData();
                }
                this._super();
            },

            _setMeasurementToolElementData: function (){
                const localStorage = registry.get("localStorage");
                const measurementElement = localStorage.get('measurement-elements.lastClickedElement');
                if (!_.isEmpty(measurementElement)) {
                    // Todo: test this part without setTimeout
                    // Todo: find better way to implement it without setTimeout
                    // setTimeout(() => {
                        //Set room name
                        let name = `${measurementElement.room_name}/${measurementElement.name}`;
                        let nameElement = $('.product-options-wrapper #belvg_custom_item_name'),
                            label = nameElement.parents('.field').find('> .label'),
                            labelDesc = label.find('.option-description-text');
                        nameElement.val(name).trigger("input");
                        labelDesc.text(nameElement.val());

                        //Set Width
                        let widthElement = $('.field[data-option-key="width"] .control input');
                        widthElement.val('' + measurementElement.width).trigger("change");

                        //Set Height
                        let heightElement = $('.field[data-option-key="height"] .control input');
                        heightElement.val('' + measurementElement.height).trigger("change");

                        //Set qty
                        if (measurementElement.qty > 1) {
                            let qtyElement = $('input#qty');
                            let decreaseButton = $('.qty-field button.update-cart-item.decr')
                            qtyElement.val(measurementElement.qty).trigger("change");
                            decreaseButton.removeClass('disabled');
                        }
                    // }, 2000);
                }
            },

            _createMeasurementToolHiddenField: function (){
                const localStorage = registry.get("localStorage");
                const measurementElement = localStorage.get('measurement-elements.lastClickedElement');
                if (!_.isEmpty(measurementElement)) {
                    let form = $("#product_addtocart_form");
                    const measurementToolElementId = measurementElement.entity_id;
                    let measurementToolElement =  $('<input>').attr({
                        type: 'hidden',
                        id: 'measurement_tool_element',
                        name: 'measurement_tool_element',
                        value: measurementToolElementId
                    });
                    form.append(measurementToolElement);
                }
            },

            _removeMeasurementToolElementData: function () {
                $(document).on('ajax:addToCart', function (event, resObject) {
                    const form = resObject.form;
                    const localStorage = registry.get("localStorage");
                    const measurementElement = localStorage.get('measurement-elements.lastClickedElement');
                    if (form && !_.isEmpty(measurementElement)) {
                        const customName = form.find('input#belvg_custom_item_name');
                        const roomName = measurementElement.room_name ?? '';
                        const elementName = measurementElement.name ?? '';
                        const name = `${roomName}/${elementName}`;
                        if (customName && customName.val() === name) {
                            localStorage.remove('measurement-elements.lastClickedElement')
                        }
                    }
                });
            },
            isCustomer: function () {
                const customer = customerData.get('customer');
                return customer?.()?.firstname ?? null;
            },
        });

        return $.mage.catalogAddToCart;
    }
});
