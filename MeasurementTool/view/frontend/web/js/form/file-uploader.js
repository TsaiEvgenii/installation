/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'ko',
    'Magento_Ui/js/form/element/file-uploader',
    'jquery',
    'mage/accordion'
], function (
    ko,
    FileUploaderComponent,
    $
) {

    'use strict';
    return FileUploaderComponent.extend({
        initialize: function () {
            this._super();

            this.imgPopupOpened = ko.observable(false);
            this.showPreviewButton = ko.observable(false);

            let self = this;
            self.showPreviewButton(!!this.value().length);
            this.value.subscribe(function (newValue) {
                self.showPreviewButton(!!newValue.length);
            })

            return this;
        },

        initImagePopup: function (element) {
            let self = this;
            this.imagePopup = element;

            document.onclick = function(e){
                if(e.target.isEqualNode(element)){
                    self.closeImagePopup();
                }
            };
        },

        toggleImagePopup: function () {
            if(this.imagePopup) {
                this.imgPopupOpened(!this.imgPopupOpened());
            }
        },

        closeImagePopup: function () {
            this.imgPopupOpened(false);
        },
    });
})
