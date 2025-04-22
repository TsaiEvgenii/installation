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
    'Magento_Catalog/js/price-utils',
    'BelVG_OrderUpgrader/js/model/popup',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/customer-data',
    'BelVG_OrderUpgrader/js/action/get-alternative-options',
    'BelVG_OrderUpgrader/js/action/upgrade-quote',
    'mage/validation'
], function (
    Component,
    registry,
    $t,
    $,
    ko,
    priceUtils,
    orderUpgraderPopup,
    quote,
    customer,
    customerData,
    getAlternativeOptions,
    upgradeQuote
) {
    'use strict';

    return Component.extend({
        defaults: {
            cart: null,
            materials: [],
            selectedMaterial: null,
            lastClickedElements: {},
            options: [
                {
                    code: 'energy_class',
                    label: 'Glass',
                    selectedValue: ko.observable(''),
                    values: [
                        {
                            value: '2_layer_glass',
                            label: 'Two layers',
                            priceDifference: ko.observable(''),
                            file: ''
                        },

                        {
                            value: '3_layer_glass',
                            label: 'Three layers',
                            priceDifference: ko.observable(''),
                            file: ''
                        }
                    ]
                }
            ],
            priceMap: [],
            selectedOption: [],
            material: 'tral',
            glass: '2_layer',
            isLoading: false,
            //Todo: get price format from backend
            priceFormat: {
                "pattern": "%s kr.",
                "precision": 2,
                "requiredPrecision": 2,
                "decimalSymbol": ",",
                "groupSymbol": ".",
                "groupLength": 3,
                "integerRequired": false
            },
            totalPrice: 0,
            modalDescription: $t("The window wholesaler's windows and doors are available in three quality materials with 2- or 3-layer energy glass. Compare prices and customize your choices as needed. Save your original cart to your profile for direct comparison – Note: any changes will update the entire cart."),

            listens: {
                selectedMaterial: 'selectedMaterialListener',
            },

        },

        initialize: function () {
            this._super();
            this.totalPrice(quote.totals().grand_total);
            // .initSettings();
            this.cart = customerData.get('cart');
            this.totalPrice(this.getFormattedPrice(quote.totals().grand_total, false));

            return this;
        },

        initObservable: function () {
            this._super()
                .observe([
                    'materials',
                    'selectedMaterial',
                    'material',
                    'priceMap',
                    'options',
                    'glass',
                    'isLoading',
                    'selectedOption',
                    'lastClickedElements',
                    'totalPrice'
                ]);

            return this;
        },

        setModalElement: function (element) {
            let self = this;
            if (orderUpgraderPopup.modalWindow == null) {
                orderUpgraderPopup.createPopUp(element);
                $(orderUpgraderPopup.modalWindow).on('modalopened', function () {
                    if (self.priceMap().length > 0) {
                        return;
                    }
                    self.isLoading(true);
                    const quoteItems = self.cart()?.items ?? [];
                    const cartId = quote.getQuoteId();
                    if (quoteItems.length > 0) {
                        getAlternativeOptions(cartId)
                            .then(function (responsePriceDifferenceData) {
                                self.priceMap(responsePriceDifferenceData.price_map);
                                self.initMaterials(responsePriceDifferenceData.materials_map);
                                self.options(self.parseOptions(responsePriceDifferenceData.options));

                                self.updatePricesForOptions();
                                self.options().forEach(function (option) {
                                    option.selectedValue.subscribe(function (newValue) {
                                        self.optionChangeListener(option, newValue);
                                    });
                                });
                            })
                            .catch(function (response) {
                                console.error(response)
                            })
                            .finally(function () {
                                self.isLoading(false);
                            })
                    }
                })
            }
        },

        parseOptions: function (optionsResponse) {
            let options = [];
            if (optionsResponse && optionsResponse.length) {
                optionsResponse.forEach(function(optionGroup) {
                    optionGroup = JSON.parse(optionGroup);
                    optionGroup['selectedValue'] = ko.observable('');
                    optionGroup.values.forEach(function(value) {
                        value['priceDifference'] = ko.observable('');
                    });
                    options.push(optionGroup);
                });
            }
            return options;
        },


        selectedMaterialListener: function (newValue, oldValue) {
            this.updatePricesForOptions();
        },

        optionChangeListener: function (option, newValue) {
            this.updatePricesForOptions();
        },

        initMaterials: function (materialsData) {
            let self = this;
            materialsData.forEach(material => {
                let materialCode = 'material_' + material.id;
                let priceDifference = self.getPriceDifferenceByCode(materialCode);
                if (priceDifference !== null) {
                    material.priceDifference = ko.observable(self.getFormattedPrice(priceDifference));
                }
            })

            this.materials(materialsData);
        },

        updatePricesForOptions: function () {
            let self = this;
            this.options().forEach((option) => {
                option.values.forEach((optionValue) => {
                    let codeWithMaterial = '';
                    let priceDifference = null;
                    if (self.selectedMaterial()) {
                        let materialCode = 'material_' + self.selectedMaterial();
                        codeWithMaterial = `${materialCode}:${option.code}-${optionValue.value}`;
                        let materialPriceDifference = self.getPriceDifferenceByCode(materialCode);
                        let codeWithMaterialPriceDifference = self.getPriceDifferenceByCode(codeWithMaterial);
                        priceDifference = codeWithMaterialPriceDifference - materialPriceDifference;
                    } else {
                        let code = `${option.code}-${optionValue.value}`;
                        priceDifference = self.getPriceDifferenceByCode(code);
                    }


                    if (priceDifference !== null) {
                        if (optionValue.priceDifference) {
                            optionValue.priceDifference(self.getFormattedPrice(priceDifference));
                        } else {
                            optionValue.priceDifference = ko.observable(self.getFormattedPrice(priceDifference));
                        }
                    }
                })
            })
           this.updateTotalPrice();
        },

        upgradeCart: function (form) {
            let params = [];
            if (this.selectedMaterial()) {
                params.push({
                    code: 'material',
                    value: this.selectedMaterial()
                })
            }
            this.options().forEach(option => {
                if (option.selectedValue) {
                    params.push({
                        code: option.code,
                        value: option.selectedValue()
                    })
                }
            });
            let quoteId = quote.getQuoteId();
            const cartData = customerData.get('cart');
            const storeId = cartData().storeId;
            $('body').trigger('processStart');
            upgradeQuote(quoteId, storeId, params)
                .then(function (response) {
                    location.reload();
                })
                .catch(function (response) {
                    console.error(response);
                })
                .finally(function () {
                    $('body').trigger('processStop');
                })
        },

        closePopup: function () {
            orderUpgraderPopup.closeModal();
        },
        getCheckoutMethod: function () {
            return customer.isLoggedIn() ? 'customer' : 'guest';
        },

        getMaterialPriceDifference: function (materialId) {
            return this.getPriceDifferenceByCode('material_' + materialId);
        },

        getMaterialPriceDifferenceFormatted: function (materialId) {
            const priceDifference = this.getMaterialPriceDifference(materialId);
            if (priceDifference !== null) {
                return this.getFormattedPrice(priceDifference);
            }
        },

        getOptionPriceDifference: function (optionCode, valueCode) {
            return this.getPriceDifferenceByCode(`${optionCode}-${valueCode}`);
        },

        getOptionPriceDifferenceFormatted: function (optionCode, valueCode) {
            const priceDifference = this.getOptionPriceDifference(optionCode, valueCode);
            if (priceDifference !== null) {
                return this.getFormattedPrice(priceDifference);
            }
        },

        getPriceDifferenceByCode(code) {
            for (const priceMapEntity of this.priceMap()) {
                if (priceMapEntity.id === code) {
                    return priceMapEntity.price;
                }
            }
            return null;
        },

        getFormattedPrice: function (price, showSign = true) {
            return priceUtils.formatPriceLocale(price, this.priceFormat, price !== 0 && showSign);
        },

        customRadioClicked: function(groupName, value, event) {
            let lastClickedValue = null;

            if (groupName === 'material') {
                lastClickedValue = this.selectedMaterial();
            } else {
                const option = this.options().find(opt => opt.code === groupName);
                if (option) {
                    lastClickedValue = option.selectedValue();
                }
            }

            if (lastClickedValue === value) {
                if (groupName === 'material') {
                    this.selectedMaterial(null);
                } else {
                    const option = this.options().find(opt => opt.code === groupName);
                    if (option) {
                        option.selectedValue('');
                    }
                }
            } else {
                if (groupName === 'material') {
                    this.selectedMaterial(value);
                } else {
                    const option = this.options().find(opt => opt.code === groupName);
                    if (option) {
                        option.selectedValue(value);
                    }
                }
            }

            this.updateTotalPrice();
            return true;
        },

        getSelectedOptionsPrice: function () {
            let self = this,
                additionalPrice = 0;

            this.options().find((opt) => {
                let groupName = opt.code
                let selected = opt.selectedValue();
                if (self.selectedMaterial()) {
                        let materialCode = 'material_' + self.selectedMaterial();
                        groupName = `${materialCode}:${groupName}`;
                }

                let selectedOptionPrice = self.getOptionPriceDifference(groupName, selected);
                additionalPrice += selectedOptionPrice;
            });
            if (self.selectedMaterial() && additionalPrice === 0) {
                additionalPrice += this.getMaterialPriceDifference(this.selectedMaterial());
            }


            return additionalPrice;
        },

        updateTotalPrice: function () {
            // let grandTotal = Number(quote.totals().grand_total);
            // if(optionGroupName && optionValue) {
            //     let optionPrice = optionGroupName === 'material' ?
            //         this.getMaterialPriceDifference(optionValue) :
            //         this.getOptionPriceDifference(optionGroupName, optionValue);
            //     grandTotal += optionPrice;
            // }
            // this.totalPrice(this.getFormattedPrice(grandTotal, false));


            this.totalPrice(
                this.getFormattedPrice(
                    Number(quote.totals().grand_total) + this.getSelectedOptionsPrice()
                ),
                false
            );
        }
    });
});

