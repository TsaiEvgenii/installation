define(function() {

    class RestrictionBase {
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
        object.objectData.restrictions || (object.objectData.restrictions = []);
        return object.objectData.restrictions;
    }

    function addToObject(object, restriction) {
        getObjectList(object).push(restriction);
        restriction.parent = object;
    }

    function insertIntoObject(object, restriction, position) {
        let list = getObjectList(object);
        list.splice(position, 0, restriction);
    }

    function removeFromObject(object, restriction) {
        let restrictions = getObjectList(object),
            idx = restrictions.indexOf(restriction);
        if (idx != -1) {
            restrictions.splice(idx, 1);
        }
    }

    function positionInObject(object, restriction) {
        return getObjectList(object).indexOf(restriction);
    }

    return {
        Base: RestrictionBase,
        getObjectList: getObjectList,
        addToObject: addToObject,
        removeFromObject: removeFromObject,
        positionInObject: positionInObject
    };
});
