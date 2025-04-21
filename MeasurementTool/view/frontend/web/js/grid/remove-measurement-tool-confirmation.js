/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'jquery',
    'BelVG_MeasurementTool/js/action/remove-measurement-tool',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function (
    $,
    removeMeasurementToolAction,
    $t,
    confirmationModal
) {

    $.widget('belvg.removeMeasurementTool', {
        options: {
            measurementToolId: null
        },

        _create: function () {
            this.deleteButton = this.element.find('BUTTON.delete-action');
            this._bind();
        },

        _bind: function () {
            let self = this;
            this._on(self.deleteButton, {
                click: "removeMeasurementToolConfirmation"
            });
        },

        removeMeasurementToolConfirmation: function () {
            let self = this;
            confirmationModal({
                title: $t('Delete Measurement Tool'),
                content: $t('Are you sure you want to delete this Measurement Tool?'),
                actions: {
                    confirm: function () {
                        self.removeMeasurementTool();
                    },
                    cancel: function () {
                        return false;
                    }
                }
            });
        },

        removeMeasurementTool: function () {
            let self = this;
            $(document.body).trigger('processStart');
            removeMeasurementToolAction(this.options.measurementToolId)
                .then(function (response) {
                    if (response === true) {
                        self.element.remove();
                    }
                })
                .catch(function (response) {
                    console.error(response);
                })
                .finally(function () {
                    $(document.body).trigger('processStop');
                })
        }

    })

    return $.belvg.removeMeasurementTool;
})
