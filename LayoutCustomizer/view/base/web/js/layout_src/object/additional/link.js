define(function() {

    class LinkBase {
        constructor(type, prefix = '') {
            this._type = type;
            this._prefix = prefix;
            this._name = null;
            this._ref = null;
            this._parent = null;
        }

        destroy() {
            if (this._parent) {
                removeFromObject(this._parent, this);
            }
        }

        getName() {
            return this._prefix + this._name;
        }

        get linkType() { return this._type; }

        get name() { return this._name; }
        set name(name) { this._name = name; }

        get ref() { return this._ref; }
        set ref(ref) { this._ref = ref; }

        get parent() { return this._parent; }
        set parent(parent) { this._parent = parent; }
    }

    function getObjectList(object) {
        object.objectData.links || (object.objectData.links = []);
        return object.objectData.links;
    }

    function addToObject(object, link) {
        getObjectList(object).push(link);
        link.parent = object;
    }

    function insertIntoObject(object, link, position) {
        let list = getObjectList(object);
        list.splice(position, 0, link);
    }

    function removeFromObject(object, link) {
        let links = getObjectList(object),
            idx = links.indexOf(link);
        if (idx != -1) {
            links.splice(idx, 1);
        }
    }

    function positionInObject(object, link) {
        return getObjectList(object).indexOf(link);
    }

    return {
        Base: LinkBase,
        getObjectList: getObjectList,
        addToObject: addToObject,
        removeFromObject: removeFromObject,
        positionInObject: positionInObject
    };
});
