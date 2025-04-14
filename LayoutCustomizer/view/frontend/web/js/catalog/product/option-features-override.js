/**
 * Original file is: MageWorx/OptionFeatures/view/base/web/js/catalog/product/features.js
 *
 * Override reason: mixins haven't worked in Firefox, Safary and Edge (but worked in Chrome)
 */
define([
    'jquery',
    'underscore',
    'priceBox',
    'qTip',
    'priceUtils',
    'layoutBase',
    'Magento_Catalog/product/view/validation',
    'jquery-ui-modules/accordion',
    'jquery-ui-modules/widget'
], function ($, _, priceBox, qTip, priceUtils, belvgLayout, validation) {
    'use strict';

    function initDefaultValue (optionConfig, key, option_id, values) {
        let defaultElem = values[key];
        defaultElem.id = key;
        defaultElem.price = optionConfig[option_id][defaultElem.id].prices['basePrice'].amount;
        return defaultElem;
    }

    $.widget('mageworx.optionFeatures', {

        options: {
            absolutePriceOptionTemplate: '<%= data.label %>' +
            '<% if (data.finalPrice.value) { %>' +
            ' <%- data.finalPrice.formatted %>' +
            '<% } %>'
        },

        /**
         * Triggers one time at first run (from base.js)
         * @param optionConfig
         * @param productConfig
         * @param base
         * @param self
         */

        /* Override */
        /* Move description text area for each choice into the label */

        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            $('body').trigger('processStart');

            setTimeout(function () {
                // Qty input
                $('.mageworx-option-qty').each(function () {
                    $(this).on('change', function () {
                        var optionInput = $("[data-selector='" + $(this).attr('data-parent-selector') + "']");
                        optionInput.trigger('change');
                    });
                });
                $('body').trigger('processStop');
            }, 500);

            // Option\Value Description & tooltip
            var extendedOptionsConfig = typeof base.options.extendedOptionsConfig != 'undefined' ?
                base.options.extendedOptionsConfig : {};
            let selfWidget = this;

            for (var option_id in optionConfig) {
                if (!optionConfig.hasOwnProperty(option_id)) {
                    continue;
                }

                var description = extendedOptionsConfig[option_id]['description'],
                    thumb = extendedOptionsConfig[option_id]['images_data'],
                    $option = base.getOptionHtmlById(option_id),
                    defaultElem = {};

                // Find Default value of swatches
                if (extendedOptionsConfig[option_id].values) {
                    var values = extendedOptionsConfig[option_id].values;

                    // Get all values
                    for (var key in values) {
                        if (values[key].is_default !== null && values[key].is_default !== '0') {
                            defaultElem = initDefaultValue(optionConfig, key, option_id, values);
                        }
                        let $currentOpt = $option.find('input[value=' + key + ']');
                        if($currentOpt.is(':checked')) {
                            defaultElem = initDefaultValue(optionConfig, key, option_id, values);

                            let $activeOpt = $option.find('.field.active input');
                            if($activeOpt.val() !== key ) {
                                $activeOpt.parent().removeClass('active');
                                $currentOpt.parent().addClass('active');
                            }
                            break;
                        }
                    }
                }

                if (1 > $option.length) {
                    console.log('Empty option container for option with id: ' + option_id);
                    continue;
                }

                if (this.options.option_description_enabled && !_.isEmpty(extendedOptionsConfig[option_id]['description'])) {
                    if (this.options.option_description_mode == this.options.option_description_modes.tooltip) {
                        var $element = $option.find('label span')
                            .first();
                        if ($element.length == 0) {
                            $element = $option.find('fieldset legend span')
                                .first();
                        }
                        $element.qtip({
                            content: {
                                text: description
                            },
                            style: {
                                classes: 'qtip-light'
                            },
                            position: {
                                target: false
                            }
                        });
                    } else if (this.options.option_description_mode == this.options.option_description_modes.text) {
                        // Add id from name attr to tab
                        if (optionConfig[option_id].name) {
                            $option.attr('id', optionConfig[option_id].name.replace(/ /g, '_').toLowerCase() + '_attr');
                        } else if (Object.values(optionConfig[option_id])[0]) {
                            $option.attr('id', Object.values(optionConfig[option_id])[0].name.replace(/ /g, '_').toLowerCase() + '_attr')
                        }
                    } else {
                        console.log('Unknown option mode');
                    }
                }

                let label = $option.find('label').first();
                if (label) {
                    let labelTitle = label.find('> span').html(),
                        priceText = defaultElem.id ? priceUtils.formatPrice(defaultElem.price) : '',
                        priceNum = defaultElem.price,
                        title = defaultElem.id ? defaultElem.title : '';
                    $(label).html(
                        "<p class='title-cont'>" +
                            "<span class='title'>" +
                            "<em class='title-text'>" + labelTitle + "</em>" +
                            "</span>" +
                            "<span class='option-description-text'>" + title + "</span>" +
                            "</p>");
                }
            }

            // Options Tabs
            $('.product-options-wrapper > .fieldset').accordion({
                header: "label:first-child",
                collapsible: true,
                active: false,
                option: false,
                heightStyle: "content",
            });
            // Adjust form validation
            {
                let validateOrig = $.fn.validate;
                $.fn.validate = function(options) {
                    let validator = validateOrig.call(this, options),
                        form = $(this[0]),
                        flag = 'validation-updated-layoutcustomizer';
                    if (form.attr('id') == 'product_addtocart_form' && !form.data(flag)) {
                        // Do not ignore elements
                        validator.settings.ignore = ':has(nonexistent-type)';
                        // Uncollapse fieldset accordion
                        let highlightOrig = validator.settings.highlight;
                        validator.settings.highlight = function(element, errorClass, validClass) {
                            let result = highlightOrig.call(this, element, errorClass, validClass),
                                panel = $(element).closest('[role=tabpanel]'),
                                field = panel.closest('.field'),
                                optionDiv = $(element).closest('.field.choice');
                            if (panel.is(':hidden')) {
                                field.find('label').first().trigger('click');
                            }

                            let offset = ($('nav').height() || 0);
                            setTimeout(function(){
                                $('html, body').stop().animate({
                                    scrollTop: optionDiv.offset().top - offset
                                });
                            }, 500);
                            return result;
                        }

                        form.data(flag, true); // mark form as updated
                    }
                    return validator;
                }
            }


            // Choice
            var optionList = $(".product-options-wrapper .options-list > .choice"),
                activeOption = optionList.parent().find('.active');

            var self = this;
            optionList.on("click", function (e) {
                var activeEl = $(this).closest('.options-list').find('.active')[0];

                if (activeEl) {
                    $(activeEl).removeClass('active');
                }
                $(this).addClass("active");
                self.changeLabel($(this));
            });

            selfWidget.applyChanges(base, productConfig);

            // optionUrlManagement.init(); //override - but moved to `BelVG_MageWorxUrls/js/catalog/product/url-updater`
        },

        changeLabel: function (choice) {
            var label = choice.parents('.field').find('> .label'),
                labelThumb = label.find('.thumbnail'),
                labelDescr = label.find('.option-description-text'),
                labelPrice = label.find('> .title-cont > .price'),

                //if MW option has 2 or more pictures -> get the last one
                choiceThumb = choice.find('.option_images_gallery img').clone().last(),
                choiceDescr = choice.find('> label > .title-cont > .title').html(),
                choicePrice = choice.find('> label > .title-cont > .price > .price').html() ? choice.find('> label > .title-cont > .price > .price').html() : '';

            labelThumb.html(choiceThumb);
            labelDescr.html(choiceDescr);
            labelPrice.html(choicePrice);
        },

        /* Override */

        /**
         * Triggers each time when option is updated\changed (from the base.js)
         * @param option
         * @param optionConfig
         * @param productConfig
         * @param base
         */
        update: function update(option, optionConfig, productConfig, base) {
            var $option = $(option),
                $optionQtyInput = $("[data-parent-selector='" + $option.attr('data-selector') + "']"),
                optionQty = 1,
                values = $option.val(),
                optionId = base.getOptionId($option);

            if ($optionQtyInput.length) {
                if (($option.is(':checked') || $('option:selected', $option).val())) {
                    if ($optionQtyInput.val() == 0) {
                        $optionQtyInput.val(1);
                    }
                    $optionQtyInput.attr('disabled', false);
                } else if (!$option.is(':checked') && !$('option:selected', $option).val()) {
                    if ($optionQtyInput.attr('type') != 'hidden' && $option.attr('type') != 'radio') {
                        $optionQtyInput.val(0);
                        $optionQtyInput.attr('disabled', true);
                    }
                }

                if (parseFloat($optionQtyInput.val())) {
                    optionQty = parseFloat($optionQtyInput.val());
                }

                if (values) {
                    if (!Array.isArray(values)) {
                        values = [values];
                    }

                    $(values).each(function (i, e) {
                        optionConfig[optionId][e]['qty'] = optionQty;
                    });
                }
            }
        },

        /**
         * Triggers each time after the all updates when option was changed (from the base.js)
         * @param base
         * @param productConfig
         */
        applyChanges: function (base, productConfig) {
            base.applyChanges();
        },

        /**
         * Add description to the values
         * @param $option
         * @param extendedOptionsConfig
         * @private
         */
        _addValueDescription: function _addValueDescription($option, extendedOptionsConfig) {
            var self = this,
                $options = $option.find('.product-custom-option');

            $options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = priceUtils.findOptionId($element),
                    optionConfig = extendedOptionsConfig[optionId],
                    value = extendedOptionsConfig[optionId]['values'];

                if ($element.attr('multiple')) {
                    return;
                }

                if (typeof value == 'undefined' || _.isEmpty(value)) {
                    return;
                }

                if ($element.hasClass('mageworx-swatch')) {
                    var $swatches = $element.parent().find('.mageworx-swatch-option');

                    $swatches.each(function (swatchKey, swatchValue) {
                        var valueId = $(swatchValue).attr('option-type-id');
                        if (!_.isUndefined(value[valueId]) &&
                            (!_.isEmpty(value[valueId]['description']) ||
                                !_.isEmpty(value[valueId]['images_data']['tooltip_image']))
                        ) {
                            var tooltipImage = self.getTooltipImageHtml(value[valueId]);
                            var title = '<div class="title">' + value[valueId]['title'] + '</div>';
                            $(swatchValue).qtip({
                                content: {
                                    text: tooltipImage + title + value[valueId]['description']
                                },
                                style: {
                                    classes: 'qtip-light'
                                },
                                position: {
                                    target: false
                                }
                            });
                        }
                    });
                } else {
                    var $image = $('<img>', {
                        src: self.options.question_image,
                        alt: 'tooltip',
                        "class": 'option-select-tooltip-' + optionId,
                        width: '16px',
                        height: '16px',
                        style: 'display: none'
                    });

                    $element.parent().prepend($image);
                    $element.on('change', function (e) {
                        var valueId = $element.val();
                        if (!_.isUndefined(value[valueId]) &&
                            !_.isEmpty(value[valueId]['description'])
                        ) {
                            var tooltipImage = self.getTooltipImageHtml(value[valueId]);
                            $image.qtip({
                                content: {
                                    text: tooltipImage + value[valueId]['description']
                                },
                                style: {
                                    classes: 'qtip-light'
                                },
                                position: {
                                    target: false
                                }
                            });
                            $image.show();
                        } else {
                            $image.hide();
                        }
                    });
                }

                if ($element.val()) {
                    $element.trigger('change');
                }
            });

            $options.filter('input[type="radio"], input[type="checkbox"]').each(function (index, element) {
                var $element = $(element),
                    optionId = priceUtils.findOptionId($element),
                    optionConfig = extendedOptionsConfig[optionId],
                    value = extendedOptionsConfig[optionId]['values'];

                if (typeof value == 'undefined' || !value) {
                    return;
                }

                var valueId = $element.val();
                if (_.isUndefined(value[valueId]) ||
                    _.isEmpty(value[valueId]['description'])
                ) {
                    return;
                }

                var description = value[valueId]['description'],
                    tooltipImage = self.getTooltipImageHtml(value[valueId]),
                    $image = self.getTooltipImageForOptionValue(valueId);

                $element.parent().append($image);
                $image.qtip({
                    content: {
                        text: tooltipImage + description
                    },
                    style: {
                        classes: 'qtip-light'
                    },
                    position: {
                        target: false
                    }
                });
            });
        },

        /**
         * Create image with "?" mark
         * @param valueId
         * @returns {*|jQuery|HTMLElement}
         */
        getTooltipImageForOptionValue: function getTooltipImageForOptionValue(valueId) {
            return $('<img>', {
                src: this.options.question_image,
                alt: 'tooltip',
                "class": 'option-value-tooltip-' + valueId,
                width: '16px',
                height: '16px'
            });
        },

        /**
         * Get image html, if it exists, for tooltip
         * @param value
         * @returns {string}
         */
        getTooltipImageHtml: function getTooltipImageHtml(value) {
            if (value['images_data']['tooltip_image']) {
                return '<div class="image" style="width:auto; height:auto"><img src="' +
                    value['images_data']['tooltip_image'] +
                    '" /></div>';
            } else {
                return '';
            }
        }
    });

    return $.mageworx.optionFeatures;
});
