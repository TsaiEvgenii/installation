/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
define([
    'jquery',
    'BelVG_InstallationElements/js/model/popup',
    'mage/translate',
    'mage/mage',
    'jquery-ui-modules/widget'
], function (
    $,
    installationPopup,
    $t
) {
    'use strict';

    function showLoader() {
        $('body').loader('show');
    }

    function hideLoader() {
        $('body').loader('hide');
    }

    $.widget('belvg.createEditOrderPopup', {
        /**
         *
         * @private
         */
        _create: function () {
            this.element.on('click', this._openInstallationProductEditPopup.bind(this));
        },

        _openInstallationProductEditPopup: function (event){
            event.stopPropagation();
            event.preventDefault();
            installationPopup.showModal();
        },

    });

    return $.belvg.createEditOrderPopup;
});
