/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'BelVG_InstallationElements/js/model/price-data',
    'Magento_Customer/js/customer-data',
    'uiRegistry'
], function (
    $,
    _,
    Component,
    priceDataModel,
    customerData,
    registry
) {
    'use strict';

    return Component.extend({
        defaults: {
            isAllowed: true,
        },

        initialize: function () {
            this._super()
                .initPriceDataModel();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe(['isAllowed']);

            return this;
        },

        initPriceDataModel: function (){
            let self = this;
            const cart = customerData.get('cart');

            this.setPriceDataModelData(cart());
            cart.subscribe(function (updateCartData) {
                self.setPriceDataModelData(updateCartData);
            });

            return this;
        },

        setPriceDataModelData(cartData){
            let priceData = priceDataModel.getPriceData();
            priceData['price'] = parseFloat(cartData?.belvg_installation_data?.price ?? 0);
            priceData['basePrice'] = parseFloat(cartData?.belvg_installation_data?.base_price ?? 0);
            priceData['drivingPrice'] = parseFloat(cartData?.belvg_installation_data?.driving_price ?? 0);
            const constructionPriceIncluded = cartData?.belvg_installation_data?.construction_price_included ?? false
            const internalFinishPriceIncluded = cartData?.belvg_installation_data?.internal_finish_price_included ?? false;
            let internalFinishType = '';
            if (internalFinishPriceIncluded) {
                internalFinishType = cartData?.belvg_installation_data?.internal_finish_type ?? false;
            }
            priceData['conditionsApproved'] = cartData?.belvg_installation_data?.conditions_approved ?? false;
            const livingRoomQty = cartData?.belvg_installation_data?.living_room_qty ?? 0;
            const highGroundFloorQty = cartData?.belvg_installation_data?.high_ground_floor_qty ?? 0;
            const firstFloorQty = cartData?.belvg_installation_data?.first_floor_qty ?? 0;
            priceData['livingRoomQty'] = livingRoomQty;
            priceData['highGroundFloorQty'] = highGroundFloorQty;
            priceData['firstFloorQty'] = firstFloorQty;
            priceDataModel.setPriceData(priceData);

            registry.get(
                'installation-elements-panel.switcher.settings-popup'
                , function (
                    settingsPopupElement
                ) {
                    settingsPopupElement.disposalOfConstructionWaste(constructionPriceIncluded);
                    settingsPopupElement.internalFinish(internalFinishPriceIncluded);
                    if (internalFinishType === 'strip') {
                        settingsPopupElement.internalFinishStrip(true);
                    }
                    if (internalFinishType === 'acrylic') {
                        settingsPopupElement.internalFinishAcrylic(true);
                    }
                    settingsPopupElement.livingRoomQty(livingRoomQty);
                    settingsPopupElement.highGroundFloorQty(highGroundFloorQty);
                    settingsPopupElement.firstFloorQty(firstFloorQty);
                    settingsPopupElement.livingRoomChecked(livingRoomQty !== 0);
                    settingsPopupElement.highGroundFloorChecked(highGroundFloorQty !== 0);
                    settingsPopupElement.firstFloorChecked(firstFloorQty !== 0);
                }
            );

        },

        getValue: function () {
            return this.getFormattedPrice(priceDataModel.getPrice(), true);
        },
    });
});
