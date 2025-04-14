define([
    '../../widget',
    '../../../data/helper'
], function(Widget, DataHelper) {

    class FormField extends Widget.Base {
        constructor(context, form, fieldTypeList, name, labelText, type, params, dependencies) {
            super('form-field', context, 'tr');
            this._form = form;
            this._name = name;
            this._type = type;
            this._dependencies = dependencies || [];

            this.initElements(fieldTypeList, type, labelText, params || {});
        }

        initElements(fieldTypeList, type, labelText, params) {
            let ef = this.context.elementFactory;

            // Input
            params.onChange = this.change.bind(this);
            let inputCell = ef.make('td'),
                input = new fieldTypeList[type](this.context, params);
            inputCell.appendChild(input.element);
            this._input = input;
            this.add(input);

            // Label
            let labelCell = ef.make('td'),
                label = ef.make('label', {
                    htmlFor: this._input.input ? this._input.input.id : null,
                    textContent: labelText
                });
            labelCell.appendChild(label);

            // Append elements
            this.element.appendChild(labelCell);
            this.element.appendChild(inputCell);
        }

        checkDependencies() {
            return this._dependencies.length == 0
                || this._dependencies.some(this.checkDependency.bind(this));
        }

        checkDependency(dependency) {
            let object = this.getObject();
            return !object
                ? false
                : Object.keys(dependency).every(function(key) {
                    let value = DataHelper.getField(object, key),
                        conditions = dependency[key];
                    return Object.keys(conditions).every(function(compare) {
                        let target = conditions[compare];
                        switch (compare) {
                        case '=':
                            return value == target;
                        case '>':
                            return value > target;
                        case '<':
                            return value < target;
                        case '>=':
                            return value >= target;
                        case '<=':
                            return value <= target;
                        case 'null':
                            return value === null;
                        case 'not-null':
                            return value !== null;
                        default:
                            throw "Invalid dependency condition: `" + compare + "'";
                        }
                    });
                }, this);
        }

        reset() {
            this._input.reset();
            let object = this.getObject();
            if (object) {
                let getter = (this._name.constructor == Array)
                    ? DataHelper.getFields
                    : DataHelper.getField;
                this._input.setValue(getter(object, this._name));
            }
            this.toggle(this.checkDependencies());
        }

        _update() {
            let object = this.getObject();
            if (object) {
                this.toggle(this.checkDependencies());
            }
        }

        change(input, value) {
            if (this.name.constructor == Array) {
                this._form.change(this, value);
            } else {
                let data = {};
                data[this.name] = value;
                this._form.change(this, data);
            }

        }

        getObject() {
            return this._form.hasObject()
                ? this._form.getObject()
                : null;
        }

        get input() { return this._input; }
        get name() { return this._name; }
        get form() { return this._form; }
    }

    return {Widget: FormField};
});
