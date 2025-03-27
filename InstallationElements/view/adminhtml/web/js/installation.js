define([
    'jquery',
], function($){
    'use strict';
    $.widget('belvg.installation',{
        options: {
            valueToInit: '0',
            carrierWithInstallation: [
                'dsv',
                'stc'
            ]
        },

        _create: function (config, element){
            this.changeInstallationFieldVisibility(this.options.valueToInit);
            const carrierProviderSelect = $('#carrier_provider_select');
            carrierProviderSelect.on('change', $.proxy(this.changeInstallationFieldVisibility, this, null));
        },

        changeInstallationFieldVisibility: function (val=null){
            const carrierProviderSelect = $('#carrier_provider_select');
            const installationIsSet = $('#installation-is-set_container');
            const installationIsSetSelect = $('#installation-is-set_select');
            if (this.options.carrierWithInstallation.includes(carrierProviderSelect.val())) {
                installationIsSet.show();
                installationIsSetSelect.val(val === null ? this.options.valueToInit : val);
            } else {
                installationIsSet.hide();
                installationIsSetSelect.val(val === null ? '0' : val);
            }
        }
    })

    return $.belvg.installation;
});