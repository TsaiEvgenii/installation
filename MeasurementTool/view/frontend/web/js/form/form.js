/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'Magento_Ui/js/modal/prompt'
], function (
    $,
    defaultForm,
    prompt
) {

    'use strict';
    return defaultForm.extend({
        defaults: {
            selectorPrefix: '.page-actions',
        },
        save: function (redirect, data) {
            this.validate();
            let self = this;

            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                let measurementToolName = this.source.data.name !== ''? this.source.data.name : this.createMeasurementToolName();
                //Add confirmation with name
                prompt({
                    title: $.mage.__('Measurement Tool name'),
                    content: $.mage.__($('.prompt-modal-content')),
                    modalClass: 'prompt measurement-tool-name',
                    value: measurementToolName,
                    validation: true,
                    promptField: '[data-role="promptField"]',
                    validationRules: ['required-entry'],
                    attributesForm: {
                        novalidate: 'novalidate',
                        action: ''
                    },
                    attributesField: {
                        name: 'name',
                        'data-validate': '{required:true}',
                        maxlength: '255',
                        'data-form-part': 'measurement_tool_form'
                    },
                    actions: {
                        always: function() {
                            // do something when the modal is closed
                        },
                        confirm: function (measurementToolName) {
                            self.source.set('data.name', measurementToolName);
                            self.setAdditionalData(data)
                                .submit(redirect);
                        },
                        cancel: function () {
                            // do something when the cancel button is clicked
                        }
                    }
                });
            } else {
                this.focusInvalid();
            }
        },

        createMeasurementToolName: function () {
            let now = new Date();
            let measurementToolName = '#' + now.getFullYear().toString();
            measurementToolName += now.getMonth().toString();
            measurementToolName += now.getDate().toString();
            measurementToolName += now.getHours().toString();
            measurementToolName += now.getMinutes().toString();
            measurementToolName += now.getSeconds().toString();
            return measurementToolName;
        },
    })

});
