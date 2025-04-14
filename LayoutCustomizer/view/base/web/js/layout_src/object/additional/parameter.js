define(function() {

    class ParameterBase {
        constructor(type, prefix = '') {
            this._type = type;
            this._prefix = prefix;
            this._name = null;
            this._parent = null;
            this._options = [];
        }

        destroy() {
            if (this._parent) {
                removeFromObject(this._parent, this);
            }
        }

        getName() {
            return this._prefix + this._name;
        }

        get parameterType() { return this._type; }

        get name() { return this._name }
        set name(name) { this._name = name; }

        get parent() { return this._parent; }
        set parent(parent) { this._parent = parent; }

        get options() { return this._options; }
        set options(options) { this._options = options; }
    }

    function getObjectList(object) {
        object.objectData.parameters || (object.objectData.parameters = []);
        return object.objectData.parameters;
    }

    function addToObject(object, parameter) {
        getObjectList(object).push(parameter);
        parameter.parent = object;
    }

    function insertIntoObject(object, parameter, position) {
        let list = getObjectList(object);
        list.splice(position, 0, parameter);
    }

    function removeFromObject(object, parameter) {
        let parameters = getObjectList(object),
            idx = parameters.indexOf(parameter);
        if (idx != -1) {
            parameters.splice(idx, 1);
        }
    }

    function positionInObject(object, parameter) {
        return getObjectList(object).indexOf(parameter);
    }

    return {
        Base: ParameterBase,
        getObjectList: getObjectList,
        addToObject: addToObject,
        removeFromObject: removeFromObject,
        positionInObject: positionInObject
    };
});
