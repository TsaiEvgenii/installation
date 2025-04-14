define(function() {

    class IdGenerator {
        constructor() {
            this._counters = {};
            this._prefix = '_auto';
        }

        generate(type) {
            return [this._prefix, type, this._nextIndex(type)].join('-');
        }

        get prefix() { return this._prefix; }
        set prefix(prefix) { this._prefix = prefix; }

        _nextIndex(type) {
            if (!this._counters[type]) {
                this._counters[type] = 0;
            }
            return ++this._counters[type];
        }
    }

    class Factory {
        constructor() {
            this._idGenerator = new IdGenerator();
        }

        make(type, attributes, dataset) {
            let element = document.createElement(type),
                preparedAttributes = this._prepareAttributes(type, attributes || {});
            for (let [name, value] of Object.entries(preparedAttributes)) {
                element[name] = value;
            }
            if (typeof(dataset) !== 'undefined') {
                for (let attrName in dataset) {
                    element.dataset[attrName] = dataset[attrName];
                }
            }

            return element;
        }

        _prepareAttributes(type, attributes) {
            if (attributes.id === true) {
                attributes.id = this._idGenerator.generate(type);
            }
            return attributes;
        }

        get idGenerator() { return this._idGenerator; }
    }

    return {Factory: Factory}
});
