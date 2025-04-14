define([
    './helper'
], function(DataHelper) {

    function getObjectUid(object) {
        // Use object unique (persistent) ID or object (temporary) ID string
        return object.objectData.uid || object.objectId.toString();
    }

    class Export {
        constructor(objectManager, objectHelper, mapList) {
            this._objectManager = objectManager;
            this._objectHelper = objectHelper;
            this._mapList = mapList;
        }

        extractAll(objects) {
            return objects.map(this.extract, this);
        }

        extract(object) {
            let map = this._getObjectMap(object);
            return map
                ? this._extract(map, object)
                : {};
        }

        _extract(map, object) {
            let oh = this._objectHelper,
                result = {
                    _type: oh.getType(object),
                    _subtype: oh.getSubtype(object),
                    _uid: getObjectUid(object)
                };
            map.forEach(this._extractField.bind(this, result, object));
            return result;
        }

        _extractField(data, object, field) {
            // get
            let value = this._getValue(object, field);
            // process
            value = field.list
                ? (value || []).map(this._processValue.bind(this, field))
                : this._processValue(field, value);
            // set
            this._setValue(data, field, value);
        }

        _getValue(object, field) {
            if (field.from) {
                // copy from field
                return DataHelper.getField(object, field.from);
            } else if (field.get) {
                // use getter
                return field.get(object);
            }
            throw "Missing `from' or `get' in field description";
        }

        _setValue(data, field, value) {
            if (field.to) {
                // copy to field
                DataHelper.setField(data, field.to, value)
            } else if (field.set) {
                // use setter
                field.set(data);
            }
        }

        _processValue(field, value) {
            if ((value === undefined || value === null) && field.nullable) {
                return null
            } else {
                switch (field.type) {
                case 'boolean':
                    return Boolean(value);
                case 'number':
                    return Number(value);
                case 'string':
                    return value ? value.toString() : '';
                case 'object':
                case 'params':
                    return value ? DataHelper.copy(value) : {};
                case 'Object':
                    return this.extract(value);
                case 'ref':
                    if (value) {
                        let refObject = this._objectManager.get(value);
                        if (refObject) {
                            return getObjectUid(refObject);
                        }
                    }
                    return null;
                case 'formula':
                default:
                    return DataHelper.copy(value);
                }
            }
        }

        _getObjectMap(object) {
            let type = object.objectId.type,
                map = this._mapList[type];
            if (!map) {
                throw "Export field map not found for type `" + type + "'";
            }
            return map;
        }
    }

    return Export;
});
