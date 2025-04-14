define([
    '../widget',
    '../../data/helper',
    './container',
    './form/field'
], function(Widget, DataHelper, Container, FormField) {

    function fieldOrder(field1, field2) {
        let order1 = (field1.order !== undefined) ? field1.order : 0,
            order2 = (field2.order !== undefined) ? field2.order : 0
        return order1 - order2;
    }

    class FormBase extends Widget.Base {
        constructor(context, fieldTypeList, formConfig) {
            super('form', context, 'form');
            this._fieldTypeList = fieldTypeList;

            this._body = new Container.Widget(context, 'table');
            this._element.appendChild(this._body.element);

            this._fields = {};
            this._onChange = [];
            this._onReset = [];

            // Add fields
            this.addFields(formConfig.fields || []);

            // Add handlers
            (formConfig.onChange || []).forEach(this.addOnChange, this);
            (formConfig.onReset || []).forEach(this.addOnReset, this);
        }

        addFields(fieldList) {
            fieldList.sort(fieldOrder).forEach(function(item) {
                this.addField(item.name, item.label, item.type, item.params, item.depends);
            }, this);
        }

        addField(name, label, type, params, dependencies) {
            let field = new FormField.Widget(
                this.context, this, this._fieldTypeList,
                name, label, type, params, dependencies);
            this.add(field);
            this._body.add(field);
            this._fields[name] = field;
        }

        getField(name) {
            return this._fields[name] || null;
        }

        removeField(name) {
            if (this._fields[name]) {
                this._fields[name].destroy();
                delete this._fields[name];
            }
        }

        reset() {
            this._onReset.forEach(function(callback) {
                callback(this);
            }, this);
            this._body.children.forEach(function(field) { field.reset(); });
        }

        change(field, value) {
            this._change(field, value);
            this.update(); // ??
            this._onChange.forEach(function(callback) {
                callback(field, value);
            });
        }

        _change(field, data) {
            let object = this.getObject();
            if (object) {
                DataHelper.setFields(object, data);
            }
        }

        addOnChange(callback) {
            this._onChange.push(callback);
        }

        addOnReset(callback) {
            this._onReset.push(callback);
        }

        getObject() {
            throw "Not implemented";
        }

        hasObject() {
            throw "Not implemented";
        }
    }

    return {Base: FormBase};
});
