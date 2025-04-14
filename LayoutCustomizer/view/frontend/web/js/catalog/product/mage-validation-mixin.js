define([
    'jquery'
], function($) {

    return function(mageValidation) {
        $.widget(
            'mage.validation',
            $.mage.validation,
            {
                listenFormValidateHandler: function(event, validation) {
                    if (validation.errorList.length) {
                        let element = validation.errorList[0].element,
                            form = $(element.form);
                        if (form.attr('id') == 'product_addtocart_form') {
                            // Scroll to field
                            let element = $(validation.errorList[0].element || []),
                                panel = $(element).closest('[role=tabpanel]'),
                                field = panel.closest('.field');
                            if(field[0].style.display === 'none') {
                                field = form.find("#" + field[0].id.split('_',1)[0]);
                            }
                            if (field.length && !element.attr('name').includes('special_color')) {
                                let offset = ($('nav').height() || 0); // TODO; move to theme
                                $('html, body').stop().animate({
                                    scrollTop: field.offset().top - offset
                                });
                            }
                            return;
                        }
                    }

                    this._super(event, validation);
                }
            });

        return $.mage.validation;
    }
});
