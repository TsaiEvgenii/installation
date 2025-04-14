define([
    './id',
    './types',
    '../data/helper',
], function(Id, ObjectTypes, DataHelper) {

    let Mixin = Base => class extends Base {
        get objectId() { return this._objectId; }
        set objectId(id) { this._objectId = id; }

        get objectData() { return this._objectData; }
        set objectData(objectData) { this._objectData = objectData; }
    };

    class Factory {
        constructor() {
            this._pairs = [];
            this._counters = {};
        }

        make(type, klass, ...args) {
            let result = new (this._getObjectClass(klass))(...args),
                index = this._nextIndex(type);
            result.objectId = new Id.Id(type, index);
            result.objectData = {};
            return result;
        }

        _getObjectClass(klass) {
            let pair = this._pairs.find(pair => (pair.klass == klass));
            if (!pair) {
                let objectClass = class extends Mixin(klass) {};
                pair = {klass: klass, objectClass: objectClass};
            }
            return pair.objectClass;
        }

        _nextIndex(type) {
            if (!this._counters[type]) {
                this._counters[type] = 0;
            }
            return ++this._counters[type];
        }
    }

    class Helper {
        constructor(types) {
            this._types = types;
        }

        getType(object) {
            return object.objectId.type;
        }

        // TODO: refactoring (used in editor/object-tree/object-node)
        getTypeDesc(object) {
            return this._getTypeDesc(object);
        }

        isTypeAdditional(object) {
            return this._getTypeDesc(object).additional || false;
        }

        getSubtype(object) {
            let type = this.getType(object);
            return this._getTypeDesc(type).getSubtype(object);
        }

        getSubtypeClass(type, subtype) {
            return this._getTypeDesc(type).getSubtypeClass(subtype);
        }

        getParent(object) {
            let type = this.getType(object);
            return this._getTypeDesc(type).getParent(object);
        }

        getParentId(object) {
            let parent = this.getParent(object);
            return parent ? parent.objectId.copy() : null;
        }

        isRoot(object) {
            return !this.getParent(object);
        }

        isAncestor(ancestor, object) {
            let current = object;
            while (current) {
                if (current.parent == ancestor) {
                    return true;
                }
                current = current.parent;
            }
            return false;
        }

        getChildren(object, childType = null) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type);
            if (childType === null) {
                return Object.keys(typeDesc.children)
                    .map(function(childType) {
                        return this.getChildren(object, childType);
                    }, this)
                    .reduce(function(acc, children) {
                        return acc.concat(children);
                    }, []);
            } else {
                let childItem = this._getTypeDescChildItem(typeDesc, childType);
                if (!childItem || !childItem.list) {
                    throw "Cannot get `" + childType + "'children from `" + type + "'";
                }
                return childItem.list(object);
            }
        }

        getParams(object) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type);
            return typeDesc.getParams
                ? typeDesc.getParams(object)
                : {};
        }

        getFormKeys(object) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type);
            return typeDesc.getFormKeys
                ? typeDesc.getFormKeys(object)
                : [];
        }

        forEach(root, callback, type = null) {
            if (!type || this.getType(root) == type) {
                callback(root);
            }
            this.getChildren(root).forEach(function(child) {
                this.forEach(child, callback, type);
            }, this);
        }

        canAddChild(object, child) {
            // Check if same object or ancestor
            if (object == child || this.isAncestor(child, object)) {
                return false;
            }
            // Check if child subtype is allowed
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type),
                childType = this.getType(child),
                childSubtype = this.getSubtype(child),
                childItem = this._getTypeDescChildItem(typeDesc, childType);
            return childItem
                && (!childItem.subtypes
                    || childItem.subtypes.includes(childSubtype))
                && (!childItem.canAddChild
                    || childItem.canAddChild(object, child));
        }

        addChild(object, child) {
            this.insertChild(object, child, null);
        }

        insertChild(object, child, position = null) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type),
                childType = this.getType(child),
                childItem = this._getTypeDescChildItem(typeDesc, childType);
            if (childItem) {
                if (childItem) {
                    if (childItem.insert && position !== null) {
                        childItem.insert(object, child, position);
                        return;
                    } else if (childItem.add) {
                        childItem.add(object, child);
                        return;
                    }
                }
            }
            throw "Cannot add `" + childType + "' to `" + type + "'";
        }

        removeChild(object, child) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type),
                childType = this.getType(child),
                childItem = this._getTypeDescChildItem(typeDesc, childType);
            if (childItem && childItem.remove) {
                childItem.remove(object, child);
            } else {
                throw "Cannot remove `" + childType + "' from `" + type + "'";
            }
        }

        getPosition(object, child) {
            let type = this.getType(object),
                typeDesc = this._getTypeDesc(type),
                childType = this.getType(child),
                childItem = this._getTypeDescChildItem(typeDesc, childType);
            if (!childItem || !childItem.position) {
                throw "Cannot get position of `" + childType + "' in `" + type + "'";
            }
            return childItem.position(object, child);
        }

        getName(object) {
            return object.name
                ? object.name
                : this.getDefaultName(object);
        }

        getDefaultName(object) {
            let id = object.objectId,
                subtype = this.getSubtype(object),
                parts = [id.type, id.index];
            if (subtype) {
                parts.push(subtype);
            }
            return parts.join(':');
        }

        _getTypeDesc(type) {
            let desc = this._types[type];
            if (!desc) {
                throw "Invalid type `" + type + "'";
            }
            return desc;
        }

        _getTypeDescChildItem(typeDesc, childType) {
            return typeDesc.children
                ? (typeDesc.children[childType] || null)
                : null;
        }
    }

    class Manager {
        constructor(helper, defaults = {}) {
            this._helper = helper;
            this._defaults = defaults;
            this._factory = new Factory();
            this._objectMap = new Id.Map();
        }

        make(type, subtype, ...args) {
            let klass = this._helper.getSubtypeClass(type, subtype),
                object = this._factory.make(type, klass, ...args);
            this._applyDefaults(object);
            object.objectData._removed = false;
            this._objectMap.set(object.objectId, object);
            return object;
        }

        remove(id) {
            if (this.has(id)) {
                this.get(id).objectData._removed = true;
            }
        }

        restore(id) {
            if (this.has(id)) {
                this.get(id).objectData._removed = false;
            }
        }

        destroy(id) {
            if (this.has(id)) {
                let object = this.get(id);

                // Destroy additional objects
                let oh = this._helper;
                oh.forEach(object, function(child) {
                    let type = oh.getType(child);
                    if (oh.isTypeAdditional(type)) {
                        object.destroy();
                    }
                }.bind(this));

                // Destroy
                object.destroy();

                // Remove from map
                this._objectMap.unset(id);
            }
        }

        has(id) {
            return this._objectMap.has(id);
        }

        get(id) {
            if (arguments.length == 2) {
                id = new Id.Id(arguments[0], arguments[1]); // TEST
            }
            return this._objectMap.get(id);
        }

        getRemovedObjectIds() {
            let set = new Id.Set();
            this._objectMap.keys().forEach(function(id) {
                if (this.get(id).objectData._removed) {
                    set.add(id);
                }
            }, this);
            return set;
        }

        _applyDefaults(object) {
            let subtype = this._helper.getSubtype(object),
                defaults = this._getDefaults(object.objectId.type, subtype);
            DataHelper.setFields(object, defaults);
        }

        _getDefaults(type, subtype) {
            let config = this._defaults[type];
            if (config && subtype != null) {
                config = config[subtype];
            }
            return config || {};
        }

    }

    return {
        Id: Id.Id,
        Helper: Helper,
        Manager: Manager
    };
});
