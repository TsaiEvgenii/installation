define([
    'underscore',
    'jquery',
    'uiCollection',
    'BelVG_LayoutCustomizer/js/layout/editor/editor',
    './data',
    './editor/config'
], function(_, $, Collection, Editor, Data, Config) {
    'use strict'

    return Collection.extend({
        defaults: {
            template: 'BelVG_LayoutCustomizer/layout-form/component/editor',
            editorConfig: {},
            editorAssets: {},
            dataConfig: {},
            overallWidthParamId: null,
            overallHeightParamId: null,
            overallWidthScope: null,
            overallHeightScope: null
        },

        initialize: function() {
            this._super();
            this.editorConfig = $.extend(
                true,
                {},
                Config,
                this.editorConfig,
                {
                    "Object": {
                        Hooks: {
                            _rootBlock: {
                                create: this.onCreateRootBlock.bind(this)
                            }
                        }
                    }
                });
            this._dataImport = new Data.Import(this.dataConfig);
            this._dataExport = new Data.Export(this.dataConfig);
            return this;
        },

        initEditor: function(element) {
            let editor = new Editor(element, this.editorConfig);
            _.each(this.editorAssets, function(url, id) {
                editor.drawer.addAssetUrl(id, url);
            });

            // Canvas size update
            function updateCanvasSize() {
                let wrapper = $(element).find('canvas').parent(),
                    width = wrapper.innerWidth(),
                    height = wrapper.innerHeight();
                editor.resize(width, height);
            }
            $(window).resize(updateCanvasSize);
            updateCanvasSize();

            this._editor = editor;
            this._importJson();
        },

        reset: function() {
            this._importJson();
        },

        update: function() {
            this._exportJson();
        },

        validate: function (){
            return this._editor.validateData();
        },

        onCreateRootBlock: function(context, block) {
            let om = context.objectManager;

            // Add measurements
            function addMeasurement(subtype, paramId) {
                let measurement = om.make('measurement', subtype);
                if (paramId) {
                    measurement.isCustomizable = true;
                    measurement.objectData.param_id = paramId;
                }
                block.add(measurement)
            }
            addMeasurement('width', this.overallWidthParamId);
            addMeasurement('height', this.overallHeightParamId);

            // Set dimenstions, use width and height from layout
            let source = this.source;
            function setDimension(name, dataScope) {
                let value = Number(source.get(dataScope));
                if (value) {
                    block[name] = value;
                }
            }
            setDimension('width', this._getScope(this.overallWidthScope));
            setDimension('height', this._getScope(this.overallHeightScope));
        },

        _getScope: function(scope) {
            return 'data.' + scope;
        },

        _exportJson: function() {
            let data = this._editor.exportData(),
                json = JSON.stringify(this._dataExport.process(data));
            this.source.set(this.dataScope, json);
        },

        _importJson: function() {
            let json = this.source.get(this.dataScope),
                data;
            this._editor.clear();
            if (json && (data = JSON.parse(json))) {
                this._editor.importData(this._dataImport.process(data));
            }
        }
    });
});
