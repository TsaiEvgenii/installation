define([
    '../config',
    '../../ui/widget',
    './object-editor/form'
], function(Config, Widget, ObjectForm) {

    var NoSubtype = '_empty';

    class FormList {
        constructor() {
            this._forms = {}
        }

        has(keys) {
            return Boolean(this.get(keys));
        }

        get(keys) {
            let current = this._forms;
            for (let i = 0; i < keys.length; ++i) {
                let key = keys[i];
                if (current[key]) {
                    current = current[key];
                } else {
                    return null;
                }
            }
            return current;
        }

        set(keys, form) {
            let current = this._forms;
            keys.slice(0, -1).forEach(function(key) {
                current[key] || (current[key] = {});
                current = current[key];
            });
            let last = keys[keys.length - 1];
            current[last] = form;
        }
    }

    class Editor extends Widget.Base {
        constructor(context) {
            super('object-editor', context, 'div');
            this._forms = new FormList();
            context.eventManager.subscribe(this, 'object', 'selected')
        }

        onEvent(event) {
            if (event.type != 'object' || event.name != 'selected') {
                return;
            }

            let oh = this.context.objectHelper;
            let objectId = event.data.id,
                object = this.context.objectManager.get(objectId),
                type = objectId.type,
                subtype = oh.getSubtype(object);

            // Create form
            let formKeys = [type, subtype].concat(oh.getFormKeys(object));
            if (!this._forms.has(formKeys)) {
                let config = this._getFormConfig(type, subtype),
                    form = new ObjectForm.Widget(this.context, config);
                // add to list
                this._forms.set(formKeys, form);
                // add widget child
                this.add(form);
                // add widget element
                this.element.appendChild(form.element);
            }

            // Hide all forms
            this.children.forEach(function(child) { child.hide() });

            // Initialize and show object form
            let form = this._forms.get(formKeys);
            form.objectId = event.data.id;
            form.show();
        }

        _getFormConfig(type, subtype) {
            let config = this.context.config.Form.Object[type];
            if (!config) {
                throw "Form config not found for type `" + type + "'";
            }
            if (subtype !== null) {
                config = config[subtype];
                if (!config) {
                    throw "Form config not found for type `" + type + "' "
                        + "subtype `" + subtype + "'"
                }
            }
            return config;
        }
    }

    return {Widget: Editor};
});
