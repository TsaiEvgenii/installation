/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

define([
    'Magento_Ui/js/form/element/select'
], function (Select) {
    'use strict';

    return Select.extend({
        initialize: function () {
            this._super();

            if (parseInt(this.source.data.store) !== 0 &&
                parseInt(this.source.get(this.parentScope).store_id) === 0
            ) {
                this.disabled(true);
            }

            return this;
        }
    })
});
