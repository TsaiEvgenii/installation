define(function() {

    var Type = {
        Object: 'object'
    };

    var Name = {
        All: 'all',
        Object: {
            Added: 'added',
            Removed: 'removed',
            Selected: 'selected',
            Changed: 'changed'
        }
    }

    class _Event {
        constructor(type, name, data) {
            this._type = type;
            this._name = name;
            this._data = data || {};
        }

        get type() { return this._type; }
        get name() { return this._name; }
        get data() { return this._data; }
    }

    class Manager {
        constructor() {
            this._subscribers = {};
        }

        subscribe(listener, type, name = Name.All) {
            if (typeof listener.onEvent !== 'function') {
                throw "Listener does not implement onEvent()";
            }
            this._getList(type, name).push(listener);
        }

        unsubscribe(listener, type, name) {
            let list = this._getList(type, name),
                idx = list.indexOf(listener);
            if (idx != -1) {
                list.splice(idx, 1);
            }
        }

        notify(type, name, data) {
            this._notify(new _Event(type, name, data));
        }

        _notify(event) {
            this._getList(event.type, event.name).forEach(function(subscriber) {
                subscriber.onEvent(event);
            });
            if (event.name != Name.All) {
                this._getList(event.type, Name.All).forEach(function(subscriber) {
                    subscriber.onEvent(event);
                });
            }
        }

        _getList(type, name) {
            let list = this._subscribers;
            list[type] || (list[type] = {});
            list[type][name] || (list[type][name] = []);
            return list[type][name];
        }
    }

    return {Manager: Manager};
});
