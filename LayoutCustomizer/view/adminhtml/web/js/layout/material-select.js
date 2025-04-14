define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'mage/dataPost'
], function($, Modal, dataPost) {

    return Modal.extend({
        defaults: {
            options: {
                copyUrl: null,
            },
            listing: 'layoutmaterial_listing'
        },

        openModal: function(action, layoutId) {
            this._super();
            this._currentLayoutId = layoutId;
            this._getListing().render();
        },

        closeModal: function() {
            this._super();
            // reset selections
            this._getListing().selections().selected([]);
        },

        copy: function() {
            let listing = this._getListing(),
                materialIds = listing.selections().selected();
            if (materialIds.length > 0) {
                this._copyCurrent(materialIds);
            } else {
                this.closeModal();
            }
        },

        _copyCurrent: function(materialIds) {
            dataPost().postData({
                action: this.options.copyUrl,
                data: {
                    layout_id: this._currentLayoutId,
                    material_ids: materialIds.join(',')
                }
            });
        },

        _getListing: function() {
            return this.getChild(this.listing);
        }
    });
});
