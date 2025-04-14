define([
    './helper'
], function(DataHelper) {

    class RefListMap {
        constructor() {
            this._map = {};
        }

        get(uid) {
            return this._map[uid] ? this._map[uid] : [];
        }

        add(uid, item) {
            this._map[uid] || (this._map[uid] = []);
            this._map[uid].push(item);
        }

        keys() {
            return Object.keys(this._map);
        }

        forEach(callback, context) {
            this.keys().forEach(function(uid) {
                callback.call(context, uid, this.get(uid));
            }, this);
        }
    }

    class Import {
        constructor(objectManager, objectHelper, mapList) {
            this._objectManager = objectManager;
            this._objectHelper = objectHelper;
            this._mapList = mapList;
            this._refListMap = null;
        }

        createAll(items) {
            this._refListMap = new RefListMap();

            //Quick fix for not Array object
            if(!Array.isArray(items)) items = Object.values(items);

            // Create all objects
            let result = items.map(this.create, this);

            // Make uid => objectId map
            let oh = this._objectHelper;
            function makeUidMap(rootObjects) {
                // {uid: objectId}
                let map = {};
                function _makeUidMap(map, root) {
                    let uid = root.objectData.uid;
                    if (uid) {
                        map[uid] = root.objectId.copy();
                    }
                    oh.getChildren(root).forEach(_makeUidMap.bind(null, map));
                }
                rootObjects.forEach(_makeUidMap.bind(null, map));
                return map;
            }
            let uidMap = makeUidMap(result);

            // Update references
            this._refListMap.forEach(function(uid, list) {
                let objectId = uidMap[uid];
                if (objectId) {
                    list.forEach(function(item) {
                        item.object[item.field] = objectId.copy();
                    });
                } else {
                    console.error('Object id not found by UID: ' + uid);
                }
            }, this);

            // fix references
            this._refListMap = null;

            return result;
        }

        create(data) {
            let type = this._getType(data);
            return this._create(type, data);
        }

        _create(type, data) {
            let subtype = this._getSubtype(data),
                object = this._objectManager.make(type, subtype),
                map = this._getTypeMap(type);
            // Restore unique ID
            object.objectData.uid = this._getUid(data);
            this._hydrate(map, object, data);
            return object;
        }


        _hydrate(map, object, data) {
            map.forEach(this._hydrateField.bind(this, object, data));
        }

        _hydrateField(object, data, field) {
            // get
            let value = this._getValue(data, field);
            // process
            value = field.list
                ? (value || []).map(this._processValue.bind(this, object, field))
                : this._processValue(object, field, value);
            // set
            this._setValue(object, field, value);
        }

        _getValue(data, field) {
            if (field.from) {
                // copy from field
                return DataHelper.getField(data, field.from);
            } else if (field.get) {
                // use getter
                return field.get(data);
            }
        }

        _setValue(object, field, value) {
            if (field.to) {
                // copy to field
                if (field.type == 'params' || (field.type == 'object' && field.merge)) {
                    // merge with default value
                    let fieldValue = {};
                    for (let key in value) {
                        DataHelper.setField(object, [field.to, key].join('.'), value[key]);
                    }
                } else {
                    // replace default value
                    DataHelper.setField(object, field.to, value);
                }
            } else if (field.add) {
                // add object child (children)
                let oh = this._objectHelper;
                if (field.list) {
                    value.forEach(oh.addChild.bind(oh, object));
                } else {
                    oh.addChild(object, value);
                }
            } else if (field.set) {
                // use setter
                field.set(data);
            }
        }

        _processValue(object, field, value) {
            if ((value === undefined || value === null) && field.nullable) {
                return null;
            } else {
                switch (field.type) {
                case 'boolean':
                    return Boolean(Number(value));
                case 'number':
                    return Number(value);
                case 'string':
                    return value ? value.toString() : '';
                case 'object':
                    return value ? DataHelper.copy(value) : {};
                case 'params':
                    return value ? this._processParams(object, value) : {};
                case 'Object':
                    return this.create(value);
                case 'ref': {
                    if (this._refListMap && value) {
                        this._refListMap.add(value, {object: object, field: field.to});
                    }
                }
                case 'formula':
                default:
                    return DataHelper.copy(value);
                }
            }
        }

        _processParams(object, value) {
            let params = this._objectHelper.getParams(object),
                values = {};
            for (let name in params) {
                let param = params[name];
                if (value[name] !== undefined && value[name] !== null) {
                    switch (param.type) {
                    case 'number':
                        values[name] = Number(value[name]);
                        break;
                    case 'color':
                    case 'string':
                    case 'formula':
                        values[name] = value[name] ? value[name].toString() : '';
                        break;
                    case 'select': {
                        let options = param.options || [],
                            option = options.find(function(option) {
                                return option.value == value[name];
                            });
                        if (option) {
                            values[name] = option.value;
                        }
                        break;
                    }}
                }
            }
            return values;
        }

        _getType(data) {
            return this._getRequired('_type', data);
        }

        _getSubtype(data) {
            return data._subtype;
        }

        _getUid(data) {
            return data._uid || null;
        }

        _getRequired(field, data) {
            if (data[field] === undefined) {
                throw "No `" + field + "' value provided";
            }
            return data[field];
        }

        _getTypeMap(type) {
            let map = this._mapList[type];
            if (!map) {
                throw "Import field map not found for type `" + type + "'";
            }
            return map;
        }
    }

    return Import;
});
