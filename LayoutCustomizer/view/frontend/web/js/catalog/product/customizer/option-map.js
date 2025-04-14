define(function() {

    function OptionMap(map) {
        this._mapByUuid = map;
        this._mapById = {};
        for (let uuid in map) {
            let id = map[uuid];
            this._mapById[id] = uuid;
        }
    }

    OptionMap.prototype.getUuid = function(id) {
        return this._mapById[id] || null;
    }

    OptionMap.prototype.getId = function(uuid) {
        return this._mapByUuid[uuid] || null;
    }

    return OptionMap;
});
