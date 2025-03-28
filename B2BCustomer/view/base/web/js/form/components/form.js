/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/form/form',
    'underscore',
    'mage/translate'
], function ($, uiAlert, uiConfirm, Form, _, $t) {
    'use strict';

    return Form.extend({


        save: function (redirect, data) {
            let splitInputs = $('.b2b-split-container input');
            $.each(splitInputs, function (index, field) {
                data[$(field).attr('id')] = $(field).val();
            });
            this.validate();

            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                this.setAdditionalData(data)
                    .submit(redirect);
            } else {
                this.focusInvalid();
            }
        },
    });
});
