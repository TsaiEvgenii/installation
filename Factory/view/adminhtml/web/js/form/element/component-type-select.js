/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

define([
    'Magento_Ui/js/form/element/ui-select',
    'uiRegistry',
    'underscore',
    'jquery',
    'mage/translate',
    'Magento_Ui/js/lib/key-codes',
], function(newCategory, registry, _, $, $t, keyCodes) {
    'use strict';

    return newCategory.extend({
        defaults: {
            imports: {
                'componentType': '${ $.provider }:${ $.parentScope }.types'
            }
        },

        initialize: function() {
            this._super();

            if (this.filterBy) {
                this.initFilter();
            }

            return this;
        },

        initObservable: function() {
            this._super();
            this.initialOptions = this.options();

            return this;
        },

        /**
         * Set link for filter.
         *
         * @returns {Object} Chainable
         */
        initFilter: function() {
            var filter = this.filterBy;

            this.filter(this.default, filter.field);
            this.setLinks({
                filter: filter.target,
            }, 'imports');

            return this;
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function(value, field) {
            var self = this;
            var source = this.initialOptions,
                result;
            registry.get(this.parentName + '.types', function (obj) {

                var optionType = obj.value();
                field = field || self.filterBy.field;

                result = _.filter(source, function(item) {
                    return item[field] === optionType;
                });

                self.options(result);
            });
        }
    });
});
