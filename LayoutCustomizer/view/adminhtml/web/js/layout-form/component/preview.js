define([
    'underscore',
    'jquery',
    'uiCollection',
    'BelVG_LayoutCustomizer/js/layout/preview/preview',
    './data'
], function(_, $, Collection, Preview, Data) {

    let previewConfigDefault = {
        background: 'white'
    };

    return Collection.extend({
        defaults: {
            template: 'BelVG_LayoutCustomizer/layout-form/component/preview',
            previewConfig: {},
            previewAssets: {},
            dataConfig: {},
            listens: {
                value: 'reset'
            },
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            }
        },

        initialize: function() {
            this._super();
            this.previewConfig = $.extend(true, {}, previewConfigDefault, this.previewConfig);
            this._dataImport = new Data.Import(this.dataConfig);
        },

        initPreview: function(element) {
            let preview = new Preview(element, this.previewConfig);
            _.each(this.previewAssets, function(url, id) {
                preview.drawer.addAssetUrl(id, url);
            });
            this._preview = preview;
            this._importJson();
        },

        reset: function() {
            if (this._preview) {
                this._importJson();
            }
        },

        _importJson: function() {
            let json = this.source.get(this.dataScope),
                data;
            this._preview.clear();
            if (json && (data = JSON.parse(json))) {
                this._preview.importData(this._dataImport.process(data));
            }
        }
    });
});
