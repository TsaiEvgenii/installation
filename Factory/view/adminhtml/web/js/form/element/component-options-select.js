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
                'componentType': '${ $.provider }:${ $.parentScope }.category_id'
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

            this.filter(this.default, filter.field, filter.additionalField);
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
         * @param {String} additionalField
         */
        filter: function(value, field, additionalField) {
            var self = this;
            var source = this.initialOptions,
                result = [];
            registry.get(this.parentName + '.category_id', function (obj) {

                var typeOptions = obj.getSelected()[0];
                field = field || self.filterBy.field;
                additionalField = additionalField || self.filterBy.additionalField;

                if (typeOptions) {
                    result = _.filter(source, function (item) {
                        if (typeOptions.type === 'category_colour' && item[additionalField] === typeOptions.type) {
                            return true;
                        }
                        return item[additionalField] === typeOptions.type && Number(item[field]) === typeOptions.value;
                    });
                }

                self.options(result);
            });
        }
    });
});
