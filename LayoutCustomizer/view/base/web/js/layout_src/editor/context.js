define([
    '../ui/context',
    './command',
    '../measurements/manager',
    '../object/object',
    '../object/id',
    './object/types',
    '../data/export',
    '../data/import',
    '../links/manager'
], function(
    Base,
    Command,
    MeasurementManager,
    Objekt,
    ObjectId,
    ObjectTypes,
    DataExport,
    DataImport,
    LinkManager) {

    class Context extends Base {
        constructor(rootElement, config) {
            super(rootElement, config);

            let oh = new Objekt.Helper(ObjectTypes),
                om = new Objekt.Manager(oh, this.config.Object.Defaults);

            this._objectHelper = oh;
            this._objectManager = om;

            this._commandHistory = new Command.History(this._config.General.commandHistoryLimit);

            this._measurementManager = new MeasurementManager();

            this._dataExport = new DataExport(om, oh, this._config.Data.ExportMapList);
            this._dataImport = new DataImport(om, oh, this._config.Data.ImportMapList);

            this._linkManager = new LinkManager(om);

            this._rootObjectIds = new ObjectId.List();
            this._selectedObjectIds = new ObjectId.Set();
        }

        get objectManager() { return this._objectManager; }
        get objectHelper() { return this._objectHelper; }
        get commandHistory() { return this._commandHistory; }
        get measurementManager() { return this._measurementManager; }

        get dataExport() { return this._dataExport; }
        get dataImport() { return this._dataImport; }

        get linkManager() { return this._linkManager; }

        get rootObjectIds() { return this._rootObjectIds; }
        get selectedObjectIds() { return this._selectedObjectIds; }
    }

    return Context;
});
