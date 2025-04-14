define([
    'jquery',
    'jquery-ui-modules/accordion'
], function ($) {
    'use strict';

    $.widget('belvg.optionsAccordion', $.ui.accordion, {
        options: {
            closeTriggerSelectors: '.options-list > .choice input',
        },

        closeButtonSelector: '.close-modal',

        _init: function () {
            this._super();

            this._eventListenerCloseButton();
        },

        _eventListenerCloseButton: function () {
            let self = this;
            this.element.find(this.closeButtonSelector).on('click', function (event) {
                event.preventDefault();
                let label = $(this).parent().siblings('.label');
                self._closeSection(label, event);
            });
            if(this.options.closeTriggerSelectors) {
                this.element.find(this.options.closeTriggerSelectors).on('click', function (event) {
                    if(event.isTrigger) return;
                    let label = $(this).parents('.control').siblings('.label');
                    self._closeSection(label, event, 500);
                });
            }
        },

        _closeSection: function(label, event, timeOut = 0) {
            let self = this;
            event.currentTarget = label;
            setTimeout(function() {
                self._eventHandler(event);
            }, timeOut);
        },

        _toggle: function (data) {
            this._super(data);

            let toShow = data.newPanel,
                self = this;

            if(toShow.length) {
                $(document).off('click.openedModal keydown.specialKey');
                $(document).on('click.openedModal', function(event) {
                    if (!self.element.is(event.target) &&
                        !self.element.has(event.target).length && !($(event.target).parents('.modal-slide').length)) {
                        event.currentTarget = toShow.siblings('.label');
                        self._eventHandler(event);
                    }
                });
                $(document).on('keydown.specialKey', function (e) {
                    if ( e.keyCode === 27 ) {
                        $(document).trigger('click.openedModal');
                    }
                });
            } else {
                $(document).off('click.openedModal keydown.specialKey');
            }
        },

        _toggleComplete: function (data) {
            this._super(data);

            if(data.newHeader[0] &&
                (navigator.userAgent.indexOf("Safari") != -1 || navigator.userAgent.indexOf("Firefox") != -1)) {

                var top_of_element = data.newHeader.offset().top;
                var bottom_of_element = data.newHeader.offset().top + data.newHeader.outerHeight();
                var bottom_of_screen = $(window).scrollTop() + $(window).innerHeight();
                var top_of_screen = $(window).scrollTop();

                if ((bottom_of_screen > top_of_element) && (top_of_screen > bottom_of_element)){
                    $([document.documentElement, document.body]).animate({
                        scrollTop: top_of_element
                    }, 500)
                }
            }
        },
    })

    return $.belvg.optionsAccordion;
})
