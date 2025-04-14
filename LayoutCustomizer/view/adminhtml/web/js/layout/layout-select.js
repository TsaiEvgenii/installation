define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'Magento_Ui/js/modal/confirm',
    'mage/dataPost',
    'mage/translate'
], function($, Modal, confirm, dataPost, $t) {

    return Modal.extend({
        defaults: {
            options: {
                copyUrl: null,
            },
            listing: 'layout_listing'
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
                targetLayoutIds = listing.selections().selected();
            if (targetLayoutIds.length > 0) {
                confirm({
                    title: $t('Copy Layout Drawing'),
                    content: $t('Target layouts\' drawings will be replaced. Are you sure?'),
                    actions: {
                        confirm: this._copyCurrent.bind(this, targetLayoutIds),
                        cancel: function() {}
                    }
                });
            } else {
                this.closeModal();
            }
        },

        _copyCurrent: function(targetLayoutIds) {
            dataPost().postData({
                action: this.options.copyUrl,
                data: {
                    layout_id: this._currentLayoutId,
                    target_layout_ids: targetLayoutIds.join(',')
                }
            });
        },

        _getListing: function() {
            return this.getChild(this.listing);
        }
    });
});
