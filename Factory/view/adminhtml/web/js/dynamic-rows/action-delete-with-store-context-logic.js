/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'Magento_Ui/js/dynamic-rows/action-delete',
    'mage/translate'
], function (
    ActionDelete,
    $t
) {
    'use strict';

    return ActionDelete.extend({
        // defaults: {
        //     links: {
        //         visible: 'setVisible'
        //     },
        // },

        /**
         * Delete record handler.
         *
         * @param {Number} index
         * @param {Number} id
         */
        deleteRecord: function (index, id) {
            if (parseInt(this.source.data.store) !== 0 &&
                parseInt(this.source.get(this.dataScope).store_id) === 0
            ) {
                alert($t('It is not possible to delete the data of the Global scope when the Store specific scope is active. Please change scope to Global and try again.'))
            } else {
                this.bubble('deleteRecord', index, id);
            }
        },

        initialize: function () {
            this._super();

            if (parseInt(this.source.data.store) !== 0 &&
                parseInt(this.source.get(this.dataScope).store_id) === 0
            ) {
                this.visible(false);
            }

            return this;
        }
    });
});
