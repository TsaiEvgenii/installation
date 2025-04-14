define(function() {

    class MeasurementRestrictionBase {
        constructor(type) {
            this._type = type;
            this._parent = null;
            this._optionId = null;
            this._params = {};
        }

        destroy() {
            if (this._parent) {
                removeFromObject(this._parent, this);
            }
        }

        get restrictionType() { return this._type; }

        get parent() { return this._parent; }
        set parent(parent) { this._parent = parent; }

        get optionId() { return this._optionId; }
        set optionId(optionId) { this._optionId = optionId; }

        get params() { return this._params; }
        set params(params) { this._params = params; }
    }

    function getObjectList(object) {
        object.objectData.measurement_restrictions || (object.objectData.measurement_restrictions = []);
        return object.objectData.measurement_restrictions;
    }

    function addToObject(object, measurementRestriction) {
        getObjectList(object).push(measurementRestriction);
        measurementRestriction.parent = object;
    }

    function insertIntoObject(object, measurementRestriction, position) {
        let list = getObjectList(object);
        list.splice(position, 0, measurementRestriction);
    }

    function removeFromObject(object, measurementRestriction) {
        let measurement_restrictions = getObjectList(object),
            idx = measurement_restrictions.indexOf(measurementRestriction);
        if (idx != -1) {
            measurement_restrictions.splice(idx, 1);
        }
    }

    function positionInObject(object, measurementRestriction) {
        return getObjectList(object).indexOf(measurementRestriction);
    }

    return {
        Base: MeasurementRestrictionBase,
        getObjectList: getObjectList,
        addToObject: addToObject,
        removeFromObject: removeFromObject,
        positionInObject: positionInObject
    };
});
