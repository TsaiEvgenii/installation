define([
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, _, modal, $t) {
    'use strict';

    $.widget('belvg.productInfoPopup', {
        options: {
            hasContent: true,
        },

        _create: function() {
            this.link = this.element.find('A.how-to.link');
            this.popup = this.element.find('#product-title-popup');

            this.popupOptions = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: $t("How to measure correctly"),
                buttons: false,
                modalClass : 'product-info-popup'
            };

            modal(this.popupOptions, this.popup);

            this._bind();
        },

        _bind: function () {
            this.link.on('click', this.openModal.bind(this));
        },

        openModal: function (event) {
            //check if popupContent exists
            //if not: open link from 'measure_old_window' variable as usual
            if(Number(this.options.hasContent)) {
                event.preventDefault();
                this.popup.modal('openModal');
            }
        }
    });

    return $.belvg.productInfoPopup;

});
