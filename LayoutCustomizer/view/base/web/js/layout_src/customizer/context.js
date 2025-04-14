define([
    '../ui/context',
    '../formulae/evaluator',
    '../measurements/manager',
    '../object/object',
    '../object/id',
    '../object/types',
    '../data/export',
    '../data/export-maps/all',
    '../data/import',
    '../data/import-maps/all',
    '../links/manager'
], function(
    Base,
    FormulaEvaluator,
    MeasurementManager,
    Objekt, ObjectId, ObjectTypes,
    DataExport, ExportMapList,
    DataImport, ImportMapList,
    LinkManager) {

    class Context extends Base {
        constructor(rootElement, config) {
            super(rootElement, config);

            let oh = new Objekt.Helper(ObjectTypes),
                om = new Objekt.Manager(oh);

            this._objectHelper = oh;
            this._objectManager = om;

            this._formulaEvaluator = new FormulaEvaluator.Evaluator(this);
            this._measurementManager = new MeasurementManager(this._formulaEvaluator);

            this._dataExport = new DataExport(om, oh, ExportMapList);
            this._dataImport = new DataImport(om, oh, ImportMapList);

            this._linkManager = new LinkManager(om);

            this._rootObjectIds = new ObjectId.List();
        }

        get objectManager() { return this._objectManager; }
        get objectHelper() { return this._objectHelper; }

        get formulaEvaluator() { return this._formulaEvaluator; }

        get measurementManager() { return this._measurementManager; }

        get dataExport() { return this._dataExport; }
        get dataImport() { return this._dataImport; }

        get linkManager() { return this._linkManager; }

        get rootObjectIds() { return this._rootObjectIds; }
    }

    return Context;
});
