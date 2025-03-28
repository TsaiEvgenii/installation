/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/select',
    'jquery',
    'uiRegistry'
], function (_, Select, $, uiRegistry) {
    'use strict';

    return Select.extend({

        initialize: function () {
            this._super();
            let self = this;
            self.checkB2BFields(self.value());
            $(document).ready(function() {
                $('.admin__page-nav .admin__page-nav-item').click(function(){
                    self.checkB2BFields(self.value());
                });
            });


            return this;
        },

        onUpdate: function () {
            this.checkB2BFields(this.value());
            this._super();
        },

        checkB2BFields: function (value) {
            let b2b_groups = this.source.data.customer.b2b_groups;
            if (b2b_groups) {
                let b2bArr = b2b_groups.split(",");
                if (b2bArr.includes(value)) {
                    $('input[name*="b2b"]').not('input[name^="customer[b2b_split"]').parents('.admin__field').show();
                    return this;
                }
            }
            $('input[name*="b2b"]').not('input[name^="customer[b2b_split"]').parents('.admin__field').hide();
            setTimeout(() => {
              $('input[name*="b2b"]').not('input[name^="customer[b2b_split"]').parents('.admin__field').hide();
            }, 600);
        }


    });
});

