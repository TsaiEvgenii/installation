/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    'jquery',
    'mage/collapsible'
], function ($, collapsible) {
    'use strict';

    $.widget('belvg.collapsibleSelect', collapsible, {
        options: {
            openedState: 'opened',
            uid: ''
        },

        _open: function () {
            this._super();

            let self = this;
            $(document).on('click.closeCollapsibleSelect' + self.options.uid, function(e) {
                if (self.element.hasClass(self.options.openedState) &&
                    ((!self.element.is(e.target) && !self.element.has(e.target).length) ||
                        self.content.has(e.target).length)) {
                    self.deactivate();
                }
            });
        },

        deactivate: function () {
            this._super();

            $(document).off('click.closeCollapsibleSelect' + this.options.uid);
        },
    });

    return $.belvg.collapsibleSelect;
})
