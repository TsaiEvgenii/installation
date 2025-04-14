define(function() {

    let FieldNameSeparator = '.';
    function splitFieldName(name) {
        return name.split(FieldNameSeparator);
    }

    function getField(object, field) {
        let parts = splitFieldName(field),
            value = object;
        while (parts.length > 0 && value !== undefined) {
            value = value[parts.shift()];
        }
        return value;
    }

    function setField(object, field, value) {
        let parts = splitFieldName(field),
            current = object;
        while (parts.length > 1 && current != undefined) {
            current = current[parts.shift()];
        }
        if (current != undefined) {
            current[parts.shift()] = value;
        }
    }

    function getFields(object, fields) {
        let values = {};
        for (let field of fields) {
            values[field] = getField(object, field);
        }
        return values;
    }

    function setFields(object, values) {
        for (let field in values) {
            setField(object, field, values[field]);
        }
    }

    function equalObjectValues(value1, value2) {
        function includes(a, b) {
            return Object.keys(b).every(function(key) {
                return b[key] == a[key];
            });
        }
        return includes(value1, value2)
            && includes(value2, value1);
    }

    function equal(value1, value2) {
        let isObject = (value1.constructor === Object);
        if ((value2.constructor === Object) != isObject) {
            return false;
        }
        return isObject
            ? equalObjectValues(value1, value2)
            : value1 == value2;
    }

    function copy(value) {
        if (value === undefined || value === null) {
            return value;

        } else if (value.constructor == Object) {
            // Copy each field
            let result = {};
            Object.keys(value).forEach(function(key) {
                result[key] = copy(value[key]);
            });
            return result;

        } else if (value.constructor == Array) {
            // Copy each item
            return value.map(copy);

        } else {
            return value;
        }
    }

    function _merge(dst, src) {
        Object.keys(src).forEach(function(key) {
            if (dst[key] === undefined || dst[key] === null) {
                // Set value
                dst[key] = copy(src[key]);

            } else if (dst[key].constructor == Object) {
                // Merge objects
                if (src[key].constructor == Object) {
                    _merge(dst[key], src[key])
                } // or do nothing

            } else if (dst[key].constructor == Array) {
                // Merge arrays
                if (src[key].constructor == Array) {
                    dst[key] = dst[key].concat(src[key].map(copy));
                } // or do nothing

            } else {
                // Replace value
                dst[key] = copy(src[key]);
            }
        });
    }

    function merge(object1, object2) {
        _merge(object1, object2);
    }

    function merged(object1, object2) {
        let result = {};
        _merge(result, object1);
        _merge(result, object2);
        return result;
    }

    return {
        splitFieldName: splitFieldName,
        getField: getField,
        setField: setField,
        getFields: getFields,
        setFields: setFields,
        equal: equal,
        copy: copy,
        merge: merge,
        merged: merged
    };
});
