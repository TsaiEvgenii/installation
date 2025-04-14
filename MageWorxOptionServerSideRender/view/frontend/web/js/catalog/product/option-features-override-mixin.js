/**
 * Original file is: MageWorx/OptionFeatures/view/base/web/js/catalog/product/features.js
 *
 * Override reason: mixins haven't worked in Firefox, Safary and Edge (but worked in Chrome)
 */
define([
    'jquery',
    'optionsAccordion',
    'mage/loader',
], function ($) {
    'use strict';
    var optionFeaturesMixin = {
        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            // $('body').trigger('processStart');

            setTimeout(function () {
                // Qty input
                $('.mageworx-option-qty').each(function () {
                    $(this).on('change', function () {
                        var optionInput = $("[data-selector='" + $(this).attr('data-parent-selector') + "']");
                        optionInput.trigger('change');
                    });
                });
                // $('body').trigger('processStop');
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
                    //serverSideRender
                    // // Get all values
                    // for (var key in values) {
                    //     if (values[key].is_default !== null && values[key].is_default !== '0') {
                    //         defaultElem = initDefaultValue(optionConfig, key, option_id, values);
                    //     }
                    //     let $currentOpt = $option.find('input[value=' + key + ']');
                    //     if($currentOpt.is(':checked')) {
                    //         defaultElem = initDefaultValue(optionConfig, key, option_id, values);
                    //
                    //         let $activeOpt = $option.find('.field.active input');
                    //         if($activeOpt.val() !== key ) {
                    //             $activeOpt.parent().removeClass('active');
                    //             $currentOpt.parent().addClass('active');
                    //         }
                    //         break;
                    //     }
                    // }
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

                //serverside render see app/design/frontend/BelVG/vinduesgrossisten/Magento_Catalog/templates/product/view/options/type/select.phtml
                // let label = $option.find('label').first();
                // if (label) {
                //     let labelTitle = label.find('> span').html(),
                //         priceText = defaultElem.id ? priceUtils.formatPrice(defaultElem.price) : '',
                //         priceNum = defaultElem.price,
                //         title = defaultElem.id ? defaultElem.title : '';
                //     $(label).html(
                //         "<p class='title-cont'>" +
                //             "<span class='title'>" +
                //             "<em class='title-text'>" + labelTitle + "</em>" +
                //             "<em class='price' attr-price=" + priceNum + ">" + priceText + "</em>" +
                //             "</span>" +
                //             "<span class='option-description-text'>" + title + "</span>" +
                //             "</p>");
                // }
            }

            // Options Tabs
            $('.product-options-wrapper > .fieldset').optionsAccordion({
                header: ".field:not(.dimensions) > .label:first-child",
                collapsible: true,
                active: false,
                option: false,
                heightStyle: "content",
                animate: { duration: 300 }
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
                                field = panel.closest('.field');
                            if (panel.is(':hidden')) {
                                field.find('label').first().trigger('click');
                            }
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

            function changeLabel(choice) {
                var label = choice.parents('.field').find('> .label'),
                    labelThumb = label.find('.thumbnail'),
                    labelDescr = label.find('.option-description-text'),
                    labelPrice = label.find('> .title-cont > .title .price'),

                    //if MW option has 2 or more pictures -> get the last one
                    choiceThumb = choice.find('.option_images_gallery img').clone().last(),
                    choiceDescr = choice.find('> label > .title-cont > .title').html(),
                    choicePrice = choice.find('> label > .title-cont > .price > .price').html() ? choice.find('> label > .title-cont > .price > .price').html() : '';

                labelThumb.html(choiceThumb);
                labelDescr.html(choiceDescr);
                labelPrice.html(choicePrice);
            };
            var self = this;
            optionList.on("click", function (e) {
                const optionField = $(this).parents('.field.family-products');
                if (optionField.length > 0) {
                    if ($(this).find('a.label').attr('href')) {
                        // Before redirect for material option
                        const controlDiv = $(this).parents('div.control');
                        if (controlDiv.length) {
                            controlDiv.hide();
                        }
                        $('body').loader('show');
                    }
                } else {
                    var activeEl = $(this).closest('.options-list').find('.active')[0];

                    if (activeEl) {
                        $(activeEl).removeClass('active');
                    }
                    $(this).addClass("active");
                    self.changeLabel($(this));
                }
            });
            // selfWidget.applyChanges(base, productConfig);

            // optionUrlManagement.init(); //override - but moved to `BelVG_MageWorxUrls/js/catalog/product/url-updater`
        },
    }
    return function (targetWidget) {
        $.widget('mageworx.optionFeatures', targetWidget, optionFeaturesMixin);
        return $.mageworx.optionFeatures;
    };
});
