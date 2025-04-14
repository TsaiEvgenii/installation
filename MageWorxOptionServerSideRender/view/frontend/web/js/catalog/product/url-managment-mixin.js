define([
    'jquery',
    'underscore',
    'BelVG_MageWorxUrls/js/catalog/product/url-parser'
], function ($, _, urlParser) {
    'use strict';

    return function (urlManagement) {
        _.extend(urlManagement.prototype, {
            handleFirstRun: function() {
                $(document).trigger('handleFirstRun-handleFirstRun', this);
            },
            initChangeOption: function() {
                var self = this;

                $(document).on('click', self.options.radio_selector, function(){
                    self.changeUrlForRadioOption(this);
                });

                $(document).on('change', self.options.text_selector, function(){
                    self.changeUrl(this);
                });

                $(document).on('change', self.options.section_sizes_selector, function(){
                    self.changeUrlForSectionSizes();
                });

                //Observe changes of Special color select
                $(document).on('change', '.special-color-select > input[type=hidden]', function(){
                    self.changeUrlForRadioOption(this);
                });
            },
            changeUrlForRadioOption: function(item) {
                var self = this;

                var option_id = $(item).parents(self.options.el_options_list).attr('id');

                option_id = option_id.replace(/-list/g,'');
                var option_value = $(item).attr('value');

                if($(item).attr('name').includes('special_color_ral')) {
                    option_value = $(item).parents('.field.choice').children('input').val() + ':' + $(item).val();
                } else {
                    var specialColorSelect = $(item).parent().find('.special-color-select > input');

                    if(specialColorSelect.length > 0) option_value += ':' + specialColorSelect.val();
                }

                self.handleAttributeParam(option_id, option_value);
            },
        });

        return urlManagement;
    };
});
