define(function() {

    class Id {
        static fromString(string) {
            let [type, index] = string.split(':');
            return new Id(type, index);
        }

        constructor(type, index) {
            this._type = type;
            this._index = index;
        }

        copy() {
            return new Id(this._type, this._index);
        }

        isSame(other) {
            return other !== null
                && this._type == other.type
                && this._index == other.index;
        }

        toString() {
            return this._type + ':' + this._index;
        }

        get type() { return this._type; }
        get index() { return this._index; }
    }

    class Map {
        constructor() {
            this._map = {};
        }

        set(id, value) {
            this._map[id.type] || (this._map[id.type] = {});
            this._map[id.type][id.index] = value;
        }

        unset(id) {
            if (this.has(id)) {
                delete this._map[id.type][id.index];
            }
        }

        get(id) {
            return this.has(id)
                ? this._map[id.type][id.index]
                : null;
        }

        has(id) {
            return this._map.hasOwnProperty(id.type)
                && this._map[id.type].hasOwnProperty(id.index);
        }

        clear() {
            this._map = {};
        }

        keys() {
            let result = [];
            Object.keys(this._map).forEach(function(type) {
                Object.keys(this._map[type]).forEach(function(index) {
                    result.push(new Id(type, index));
                });
            }, this);
            return result;
        }
    }

    class Set {
        constructor() {
            this._map = new Map();
        }

        copy() {
            let result = new Set();
            this.toArray().forEach(result.add, result);
            return result;
        }

        add(id) {
            this._map.set(id, true);
        }

        remove(id) {
            this._map.unset(id);
        }

        has(id) {
            return this._map.has(id);
        }

        clear() {
            this._map.clear();
        }

        sum(other) {
            let result = this.copy();
            other.toArray().forEach(result.add, result);
            return result;
        }

        diff(other) {
            let result = this.copy();
            other.toArray().forEach(result.remove, result);
            return result;
        }

        toArray() {
            return this._map.keys();
        }

        toString() {
            return '(' + this.toArray().map(id => id.toString()).join(' ') + ')';
        }
    }

    class List {
        constructor() {
            this._list = [];
        }

        has(id) {
            return this._getIndex(id) != -1;
        }

        add(id) {
            this.insert(id.copy(), this._list.length);
        }

        insert(id, position) {
            if (this.has(id)) {
                throw "ID " + id.toString() + " is already added";
            }
            this._list.splice(position, 0, id.copy());
        }

        remove(id) {
            let idx = this._getIndex(id);
            if (idx != -1) {
                this._list.splice(idx, 1);
            }
        }

        position(id) {
            return this._getIndex(id);
        }

        toArray() {
            return this._list;
        }

        _getIndex(id) {
            return this._list.findIndex(function(item) {
                return item.isSame(id);
            });
        }
    }

    return {
        Id: Id,
        Map: Map,
        Set: Set,
        List: List
    };
});
