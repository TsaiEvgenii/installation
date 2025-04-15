define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function(dynamicRows) {

    return dynamicRows.extend({
        defaults: {},

        initialize: function() {
            this._super();
            this.on('recordData', this.validateUnique.bind(this));
        },

        validateUnique: function(rowData) {
            // Collect similar records by key
            // {key1: [record1, record2, ..], key2: [record1, record2, ...]}
            let recordsByKey = {};
            this.elems().forEach(function(record) {
                let key = this.getRecordKey(record);
                if (key !== null) {
                    recordsByKey[key] || (recordsByKey[key] = []);
                    recordsByKey[key].push(record);
                }
            }, this);

            // Highlight non-unique items
            for (let key in recordsByKey) {
                let records = recordsByKey[key];
                let method = (records.length == 1)
                    ? this.unmarkNonUniqueRecord
                    : this.markNonUniqueRecord;
                records.forEach(method, this);
            }
        },

        getRecordKey: function(record) {
            throw "not implemented";
        },

        markNonUniqueRecord: function(record) {
            throw "not implemented";
        },

        unmarkNonUniqueRecord: function(record) {
            throw "not implemented";
        }
    });
});
