/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */
define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'uiRegistry',
    'mage/translate',
    'jquery',
    'ko',
    'BelVG_InstallationElements/js/model/popup',
    'BelVG_InstallationElements/js/model/price-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'BelVG_InstallationElements/js/action/add-installation-product',
    'BelVG_MeasurementRequest/js/action/add-measurement-product',
    'mage/validation'
], function (
    Component,
    registry,
    $t,
    $,
    ko,
    installationPopup,
    priceDataModel,
    quote,
    customerData,
    addInstallationProductAction,
    addMeasurementProductAction
) {
    'use strict';

    return Component.extend({
        defaults: {
            cart: null,
            quoteItemQty: 0,
            livingRoomQty: 0,
            highGroundFloorQty: 0,
            firstFloorQty: 0,
            aboveQty: 0,
            livingRoomChecked: false,
            highGroundFloorChecked: false,
            firstFloorChecked: false,
            aboveChecked: false,
            checkboxesNames: [
                'livingRoomChecked',
                'highGroundFloorChecked',
                'firstFloorChecked'
            ],
            priceDataComponents: ['livingRoomQty','highGroundFloorQty','firstFloorQty'],
            disposalOfConstructionWaste: false,
            internalFinish: false,
            internalFinishStrip: false,
            internalFinishAcrylic: false,
            conditionsApproved: false,
            "listens": {
                "disposalOfConstructionWaste": "disposalOfConstructionWasteChanged",
                "internalFinish": "internalFinishChanged",
                "internalFinishStrip": "internalFinishStripChanged",
                "internalFinishAcrylic": "internalFinishAcrylicChanged",
                "conditionsApproved": "conditionsApprovedChanged",
            }
        },

        initialize: function () {
            this._super()
                .initSettings();

            return this;
        },

        initObservable: function (){
            this._super()
                .observe([
                    'quoteItemQty',
                    'livingRoomQty',
                    'highGroundFloorQty',
                    'firstFloorQty',
                    'aboveQty',
                    'livingRoomChecked',
                    'highGroundFloorChecked',
                    'firstFloorChecked',
                    'aboveChecked',
                    'disposalOfConstructionWaste',
                    'internalFinish',
                    'internalFinishStrip',
                    'internalFinishAcrylic',
                    'conditionsApproved'
                ]);

            return this;
        },

        initSettings: function () {
            let self = this;
            this.cart = customerData.get('cart');
            this.initFloorHeightSettings(this.cart());
            this.cart.subscribe((updatedCart) => {
                const currentQuoteItemQty = updatedCart?.['belvg_installation_data']?.['qty'] ?? 0
                self.quoteItemQty(currentQuoteItemQty);

                self.initFloorHeightSettings(updatedCart);
            })

            return this;
        },

        initFloorHeightSettings: function (cartData) {
            const priceData = priceDataModel.getPriceData();

            this.conditionsApproved(priceData['conditionsApproved']);
            this.livingRoomQty(priceData?.['livingRoomQty'] ?? 0);
            if (this.livingRoomQty() > 0) {
                this.livingRoomChecked(true);
            }
            this.highGroundFloorQty(priceData?.['highGroundFloorQty'] ?? 0);
            if (this.highGroundFloorQty() > 0) {
                this.highGroundFloorChecked(true);
            }
            this.firstFloorQty(priceData?.['firstFloorQty'] ?? 0);
            if (this.firstFloorQty() > 0) {
                this.firstFloorChecked(true);
            }

            this.disposalOfConstructionWaste(priceData['disposalOfConstructionWastePrice'] !== 0);
            this.internalFinish(priceData['internalFinishPrice'] !== 0);
            if (this.internalFinish() === false) {
                this.internalFinishStrip(false);
                this.internalFinishAcrylic(false);
            }
        },

        getPrice: function (){
            return this.getFormattedPrice(priceDataModel.getPrice(), false);
        },

        getAssemblyTitle: function () {
            return $t('Assembly (%1 ps.)')
                .replace('%1', parseInt(this.cart()?.belvg_installation_data?.qty ?? 0));
        },

        getBasePrice: function (){
            return this.getFormattedPrice(priceDataModel.getPriceData()['basePrice'], false);
        },

        getDrivingTitle: function (){
            return $t('Driving');
        },

        allowDrivingPrice: function (){
            return (priceDataModel.getPriceData()['drivingPrice'] ?? 0) !== 0;
        },

        getDrivingPrice: function (){
            return this.getFormattedPrice(priceDataModel.getPriceData()['drivingPrice'], false);
        },

        getMeasurementTitle: function () {
            return $t('Inspection measurement');
        },

        allowMeasurementPrice: function () {
            return (this.cart()?.belvg_installation_data?.measurement_price ?? 0) !== 0;
        },

        getMeasurementPrice: function () {
            return this.getFormattedPrice(this.cart()?.belvg_installation_data?.measurement_price ?? 0, false);
        },

        getHighGroundFloorTitle: function () {
            return $t('High ground floor (%1 ps.)')
                .replace('%1', parseInt(priceDataModel.getPriceData()['highGroundFloorQty']));
        },

        allowHighGroundFloorPrice: function (){
            return (priceDataModel.getPriceData()['highGroundFloorQty'] ?? 0) !== 0;
        },

        getHighGroundFloorPrice: function (){
            const highGroundFloor = priceDataModel.getPriceData()['highGroundFloorQty'] * (this.cart()?.belvg_installation_data?.high_ground_floor_price_for_one_item ?? 0);
            return this.getFormattedPrice(highGroundFloor, false);
        },

        getFirstFloorTitle: function () {
            return $t('First floor (%1 ps.)')
                .replace('%1', parseInt(priceDataModel.getPriceData()['firstFloorQty']));
        },

        allowFirstFloorPrice: function (){
            return (priceDataModel.getPriceData()['firstFloorQty'] ?? 0) !== 0;
        },

        getFirstFloorPrice: function (){
            const firstFlorPrice = priceDataModel.getPriceData()['firstFloorQty'] * (this.cart()?.belvg_installation_data?.first_floor_price_for_one_item ?? 0);
            return this.getFormattedPrice(firstFlorPrice, false);
        },

        getDisposalTitle: function () {
            return $t('Disposal (%1 ps.)')
                .replace('%1', parseInt(this.cart()?.belvg_installation_data?.qty ?? 0));
        },

        allowDisposalOfConstructionWastePrice: function (){
            return (priceDataModel.getPriceData()['disposalOfConstructionWastePrice'] ?? 0) !== 0;
        },

        getDisposalOfConstructionWastePrice: function (){
            return this.getFormattedPrice(priceDataModel.getPriceData()['disposalOfConstructionWastePrice'], false);
        },

        conditionsApproved: function () {
            return priceDataModel.getPriceData()['conditionsApproved'] ?? false;
        },

        getConditionsApprovedLabel: function (){
            return $.mage.__('I have read and accept the <a href="%1" target="_blank">Window Wholesaler Conditions </a> for Installation').replace('%1', window.installationServiceConditions);
        },

        getInternalFinishTitle: function () {
            return $t('Internal finish (%1 ps.)')
                .replace('%1', parseInt(this.cart()?.belvg_installation_data?.qty ?? 0));
        },

        allowInternalFinishPrice: function () {
            return (priceDataModel.getPriceData()['internalFinishPrice'] ?? 0) !== 0;
        },

        getInternalFinishPrice: function (){
            return this.getFormattedPrice(priceDataModel.getPriceData()['internalFinishPrice'], false);
        },

        increaseQty: function (entity) {
            let qtyEntity = entity + 'Qty';

            // first - check the current value not to be more than quoteQty
            if(this[qtyEntity]() === this.quoteItemQty()) {
                this.validateQty();
                return;
            }

            let self = this;
            let floorsQty = [...this.priceDataComponents];
            let currentFloorQtyIndex = floorsQty.indexOf(qtyEntity);
            if(currentFloorQtyIndex !== -1) {
                // get the rest floor Qty names
                floorsQty.splice(currentFloorQtyIndex, 1);
            }
            // get the rest floor Qty names with their Qty value
            floorsQty = Object.assign(...floorsQty.map(function(entName) {return {[entName]: self[entName]()};}));
            // get the Max Qty value
            let maxQtyValue = Math.max.apply(null, Object.values(floorsQty));
            // get the Max Qty value's name
            let maxQtyValueFloor = Object.keys(floorsQty).find(function(key) {return floorsQty[key] === maxQtyValue});
            // get the component and decrease it
            this[maxQtyValueFloor](maxQtyValue-1);

            if (this.validateQty()) {
                this[qtyEntity](this[qtyEntity]() + 1);
                this.updatePriceData();
            }
        },

        decreaseQty: function (entity) {
            let qtyEntity = entity + 'Qty';

            if (this[qtyEntity]() > 0) {

                this[qtyEntity](this[qtyEntity]() - 1);
                this.livingRoomQty(this.livingRoomQty() + 1);

                this.updatePriceData([qtyEntity]);
            }
        },

        updatePriceData: function (priceDataComponents = this.priceDataComponents){
            let self = this;
            priceDataComponents.forEach((priceDataComponent) =>{
                let priceData = priceDataModel.getPriceData();
                priceData[priceDataComponent] = self[priceDataComponent]();
                priceDataModel.setPriceData(priceData);
            })
        },

        validateQty: function () {
            let valid =  (this.livingRoomQty() + this.highGroundFloorQty() + this.firstFloorQty()) < this.quoteItemQty();

            if (valid === false) {
                registry.get('installation-elements-panel.switcher.settings-popup.messages', function (installationPopupMessages) {
                    installationPopupMessages.removeAll();
                        let messageContainer = installationPopupMessages.messageContainer;
                        let message = $t('You are trying to add more items than there are in the cart');
                        messageContainer.addErrorMessage({message: message});
                })
            }

            return valid;
        },

        validateConditionsApproval: function () {
            if (this.conditionsApproved() === false) {
                registry.get('installation-elements-panel.switcher.settings-popup.messages', function (installationPopupMessages) {
                    installationPopupMessages.removeAll();
                    let messageContainer = installationPopupMessages.messageContainer;
                    let message = $t('Conditions for installation are not approved.');
                    messageContainer.addErrorMessage({message: message});
                })
            }
            return this.conditionsApproved();
        },

        disposalOfConstructionWasteChanged: function (value) {
            let priceData = priceDataModel.getPriceData();
            const constructionPrice = this.cart()?.belvg_installation_data?.construction_price ?? 0
            if (value) {
                priceData['disposalOfConstructionWastePrice'] = constructionPrice;
            } else {
                priceData['disposalOfConstructionWastePrice'] = 0;
            }

            priceDataModel.setPriceData(priceData);
        },

        conditionsApprovedChanged: function (value) {
            let priceData = priceDataModel.getPriceData();
            // const constructionPrice = this.cart()?.belvg_installation_data?.construction_price ?? 0
            priceData['conditionsApproved'] = value;
            priceDataModel.setPriceData(priceData);
        },

        internalFinishChanged: function (value){
            let priceData = priceDataModel.getPriceData();
            const internalFinishPrice = this.cart()?.belvg_installation_data?.internal_finish_price ?? 0
            if (value) {
                priceData['internalFinishPrice'] = internalFinishPrice;
            } else {
                priceData['internalFinishPrice'] = 0;
            }

            priceDataModel.setPriceData(priceData);
        },

        internalFinishStripChanged: function (value) {
            if (value) {
                this.internalFinishAcrylic(false);
            }
            this.internalFinish(value || this.internalFinishAcrylic())
        },

        internalFinishAcrylicChanged: function (value) {
            if (value) {
                this.internalFinishStrip(false);
            }
            this.internalFinish(value || this.internalFinishStrip())
        },

        setModalElement: function (element) {
            if (installationPopup.modalWindow == null) {
                installationPopup.createPopUp(element);
            }
        },
        add: function (form) {
            if (this.validateConditionsApproval() === false) {
                return;
            }
            let self = this;
            const quoteId = quote.getQuoteId();
            const storeId = this.cart().storeId;
            let priceData = priceDataModel.getPriceData();
            let internalFinish = priceData['internalFinishPrice'] !== 0;
            let internalFinishType = internalFinish === false ? null : this.internalFinishStrip() ? 'strip' : 'acrylic';

            let installationData = {
                'disposal_of_construction_waste': priceData['disposalOfConstructionWastePrice'] !== 0,
                'internal_finish': internalFinish,
                'internal_finish_type': internalFinishType,
                'installation_living_room_qty': this.livingRoomQty(),
                'installation_high_ground_floor_qty': this.highGroundFloorQty(),
                'installation_first_floor_qty': this.firstFloorQty(),
                'installation_above_first_floor_qty': this.aboveQty(),
                'additional_prices':  priceDataModel.getAdditionalPricesData(),
                'conditions_approved': this.conditionsApproved()
            }

            $('body').trigger('processStart');
            addInstallationProductAction(quoteId, storeId, installationData)
                .then(function (response) {
                    // Add measurement product
                    let totalQty = self.livingRoomQty() + self.highGroundFloorQty() +
                        self.firstFloorQty() + self.aboveQty();

                    // Get week number from model or use null if not available
                    let weekNumber = null;

                    return addMeasurementProductAction(quoteId, storeId, totalQty, weekNumber);
                })
                .then(function() {
                    installationPopup.closeModal();
                    window.location.reload();
                })
                .catch(function (response) {
                    console.error(response);
                })
                .finally(function () {
                    $('body').trigger('processStop');
                });

            return false;
        },

        closePopup: function () {
            installationPopup.closeModal();
        },

        getTooltipText: function () {
            return $t('The measurements are from the ground to the top edge of the opening in the wall where the window/door should be installed.');
        }
    });
});
