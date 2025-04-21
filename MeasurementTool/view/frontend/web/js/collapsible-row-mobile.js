/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'jquery',
    'mage/translate',
    'mage/collapsible'
], function ($, $t, collapsible) {
    'use strict';

    $.widget('belvg.collapsibleRow', collapsible, {
        options: {
            openedState: 'opened',
            uid: '',
            closeButton: `<button class="action-close"></button>`,
            mobileTitleText: $t('Survey'),
            mobileTitle: `<div class="mobile-title">Survey</div>`
        },

        _processPanels: function () {
            this._super();

            if(this.content) {
                this.content.prepend(this.options.mobileTitle);
                this.content.prepend(this.options.closeButton);

                let self = this;
                $(this.options.closeButton).on('click', function() {
                    self.deactivate();
                })
            }
        },

        _open: function () {
            this._super();

            $(document.body).addClass('_has-modal');
            let self = this;
            $(document).on('click.closeCollapsibleRow', function(e) {
                if (self.element.hasClass(self.options.openedState) &&
                    ( (!self.element.is(e.target) && !self.element.has(e.target).length) ||
                        ($(e.target).hasClass('action-close') && self.element.has(e.target).length) )) {
                    self.deactivate();
                }
            });
        },

        _close: function () {
            $(document.body).removeClass('_has-modal');
            this._super();
        },

        deactivate: function () {
            this._super();

            $(document).off('click.closeCollapsibleRow');
        },

        _destroy: function () {
            this._super();
            $(document).off('click.closeCollapsibleRow');
            $(document.body).removeClass('_has-modal');

            this.content.find('.mobile-title').remove();
            this.content.find('.action-close').remove();
        }
    });

    return $.belvg.collapsibleRow;
})
