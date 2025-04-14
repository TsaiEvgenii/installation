define([
    'jquery',
    'mwImageReplacer'
], function ($, replacer) {
    'use strict';

    var additionalMixin = {
        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            $(document).on('mageworxupdatersBuild', function () {
                self.state='default';
            });
        },

        elementChange: function () {
            var valueId = this.options.$element.val();

            if (this.getOGType() != this.getOGTypeDisabled()) {
                if (!valueId && this.isEnabledOptionReplaceMode()) {
                    var sortOrder = this.getOptionValueSortOrder(this.getOptionId(), null);

                    this._removeCandidateForReplacement(sortOrder);
                    replacer.forceRefresh();
                }
            }

            this._renderImages(valueId);
            if (this.isEnabledOptionReplaceMode()) {
                replacer.replace();
            }
        },

        _renderImages: function (valueId) {
            var images = this._prepareOptionImages(valueId),
                isDefault = this.options.$element[0].checked;

            if (Object.keys(images).length > 0) {
                if (this.getOGType() == this.getOGTypeBesideOption() || this._isValueSelected()) {
                    var $imagesContainer = this.getOptionGalleryContainer();

                    // add thumbnail to label
                    var attrCont = $imagesContainer.parents('.field')[1],
                        attrLabel = $(attrCont).find('> .label');

                    if (!attrLabel.find('> .thumbnail')[0]) {
                        attrLabel.append('<div class="thumbnail"></div>');
                    }

                    if (!$(attrLabel).find(' > .thumbnail > img').length) {
                        if (images[Object.keys(images)[0]].url) {
                            if (isDefault) {
                                $(attrLabel).find('> .thumbnail').append("<img class=\"before-lazyload\" data-src='" + images[Object.keys(images)[0]].url + "'/>");
                            }
                        }
                    }
                }
            } else if (this.isEnabledOptionReplaceMode()) {
                var sortOrder = this.getOptionValueSortOrder(this.getOptionId(), null);

                this._removeCandidateForReplacement(sortOrder);
                replacer.forceRefresh();
            }
        },
    };

    return function (targetWidget) {
        $.widget('mageworx.optionAdditionalImages', targetWidget, additionalMixin);

        return $.mageworx.optionAdditionalImages;
    };
});
