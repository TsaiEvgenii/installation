define([
    '../object/id',
    './error'
], function(ObjectId, MeasurementError) {

    class List {
        constructor() {
            this._listsByPlacement = {};
            this._noPlacementList = [];
        }

        add(level, measurement) {
            if (measurement.measurementType == 'dimension') {
                let placement = measurement.placement;
                this._listsByPlacement[placement]
                    || (this._listsByPlacement[placement] = {});
                this._listsByPlacement[placement][level]
                    || (this._listsByPlacement[placement][level] = []);
                this._listsByPlacement[placement][level].push(measurement);
            } else {
                this._noPlacementList.push(measurement);
            }
        }

        prepare() {
            let levelDistance = 30;
            let measurements = [];
            for (let placement in this._listsByPlacement) {
                let list = this._listsByPlacement[placement],
                    levels = Object.keys(list),
                    currentLevel = levels.length;
                levels.sort();
                levels.forEach(function(level) {
                    list[level].forEach(function(measurement) {
                        measurement.distance = currentLevel * levelDistance;
                        measurements.push(measurement);
                    });
                    --currentLevel;
                })
            }
            return measurements.concat(this._noPlacementList);
        }
    }

    class Manager {
        constructor(formulaEvaluator) {
            this._formulaEvaluator = formulaEvaluator;
            this.reset();
        }

        reset() {
            this._lists = new ObjectId.Map();
            this._measurementsByName = {};
            this._measurements = [];
        }

        collect(root) {
            this.reset();
            this._lists.set(root.objectId, new List());
            this._collect(root, 0, root);
        }

        validate() {
            return this._measurements.every(function(measurement) {
                let errorCode = measurement.getInputErrorCode();
                return !errorCode || errorCode == MeasurementError.Ok;
            });
        }
        validateNames(){
            let measurementNames = this._measurements.map(function (measurement){
                return measurement._name;
            })
            let duplicates = measurementNames.filter((e, i, a) => a.indexOf(e) !== i);
            return duplicates;
        }

        getByName(name) {
            return this._measurementsByName[name]
                ? this._measurementsByName[name]
                : null;
        }

        getAllByName() {
            return this._measurementsByName;
        }

        prepare(root) {
            let list = this._lists.get(root.objectId);
            if (!list) {
                throw "Dimension list not found in `" + root.objectId.toString() + "'";
            }

            let measurements = list.prepare();
            // Set formula evaluator
            measurements.forEach(function(measurement) {
                measurement.formulaEvaluator = this._formulaEvaluator;
            }, this);

            root.objectData._measurements = measurements;
        }

        _collect(root, level, block) {
            // Block measurements
            block.measurements
                .map(function(blockMeasurement) {
                    return blockMeasurement.getMeasurement();
                })
                .forEach(this._addMeasurement.bind(this, root, level));

            // Shape measurements
            block.shape.getMeasurements().forEach(
                this._addMeasurement.bind(this, root, level + 1));

            // Process children
            block.children.forEach(this._collect.bind(this, root, level + 2));
        }

        _addMeasurement(root, level, measurement) {
            // Add to named measurements
            if (measurement.name) {
                this._measurementsByName[measurement.name] = measurement;
            }

            // Add to root block list
            let list = this._lists.get(root.objectId);
            list.add(level, measurement);

            // Add to list
            this._measurements.push(measurement);
        }
    }

    return Manager;
});
