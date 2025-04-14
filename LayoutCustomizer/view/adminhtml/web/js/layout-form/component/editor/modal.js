define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'Magento_Ui/js/modal/alert'
], function($, Modal, alert) {
    'use strict'

    function __(text) {
        return $.mage.__(text);
    }

    return Modal.extend({
        defaults: {
            options: {
                resetConfirmText: __('Discard changes?'),
                cancelConfirmText: __('Discard changes?'),
                buttons: [
                    {
                        text: __('Reset'),
                        "class": '',
                        attr: {},
                        actions: ['actionReset']
                    },
                    {
                        text: __('Update and close'),
                        "class": '',
                        attr: {},
                        actions: ['actionUpdate']
                    }
                ]
            }
        },

        actionUpdate: function() {
            let validator = this._getEditor().validate();
            if (validator.isValid === false) {
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__(validator.messages.join('. ')),
                    actions: {
                        always: function () {
                        }
                    }
                });
            } else {
                this._getEditor().update();
                this.closeModal();
            }
        },

        actionReset: function() {
            if (confirm(this.options.resetConfirmText)) {
                this._getEditor().reset();
            }
        },

        actionCancel: function() {
            if (confirm(this.options.cancelConfirmText)) {
                this.closeModal();
                this._getEditor().reset();
            }
        },

        _getEditor: function() {
            return this.getChild('editor');
        }
    });
});
