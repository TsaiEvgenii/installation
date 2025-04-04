/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
define([
    'jquery',
    'ko',
    'Magento_Customer/js/customer-data',
], function (
    $,
    ko,
    customerData
) {
    'use strict';

    let priceData = ko.observable({
        'price': 0,
        'basePrice': 0,
        'disposalOfConstructionWastePrice': 0,
        'internalFinishPrice': 0,
        'drivingPrice': 0,
        'measurementPrice': 0,
        'livingRoomQty': 0,
        'highGroundFloorQty': 0,
        'firstFloorQty': 0,
        'additionalPrices': {},
        'conditionsApproved': false
    });

    let price = ko.computed(function (){
        const cart = customerData.get('cart');
        let data = priceData();
        let livingRoomPrice = data['livingRoomQty'] * (cart()?.belvg_installation_data?.living_room_price_for_one_item ?? 0);
        let highGroundFloorPrice = data['highGroundFloorQty'] * (cart()?.belvg_installation_data?.high_ground_floor_price_for_one_item ?? 0);
        let firstFlorPrice = data['firstFloorQty'] * (cart()?.belvg_installation_data?.first_floor_price_for_one_item ?? 0);
        let measurementPrice = cart()?.belvg_installation_data?.measurement_price ?? 0;

        let price = data['basePrice'] +
            data['drivingPrice'] +
            data['disposalOfConstructionWastePrice'] +
            data['internalFinishPrice'] +
            measurementPrice +
            livingRoomPrice +
            highGroundFloorPrice +
            firstFlorPrice;

        if (Object.keys(data['additionalPrices']).length > 0) {
            Object.values(data['additionalPrices']).forEach((additionalPriceData) => {
                price += additionalPriceData.price;
            })
        }

        priceData()['price'] = price;
        return price;
    },this);
    function getAdditionalPricesData() {
        let additionalPrices = {};
        let data = priceData();
        for (const [key, value] of Object.entries(data['additionalPrices'] ?? {})) {
            if(value.price && value.label){
                additionalPrices[key] = value;
            }
        }
        return additionalPrices;
    }

    return {
        getPriceData: function () {
            return priceData();
        },
        getAdditionalPricesData: function () {
            return getAdditionalPricesData();
        },
        setPriceData: function (data){
            priceData(data);
        },
        getPrice: function () {
            return price();
        },

        setMeasurementPrice: function(price) {
            let data = priceData();
            data.measurementPrice = price;
            priceData(data);
        }
    };
});