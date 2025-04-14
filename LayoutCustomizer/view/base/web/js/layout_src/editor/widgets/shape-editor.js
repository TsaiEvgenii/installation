define([
    '../../blocks/shapes/all',
    '../../ui/widget',
    './form',
    '../../ui/widgets/fields/select'
], function(ShapeList, Widget, Form, Select) {

    function makeShape(type) {
        if (!ShapeList[type]) {
            throw "Invalid shape type `" + type + "'";
        }
        return new ShapeList[type].Shape();
    }

    class ShapeEditor extends Widget.Base {
        constructor(context) {
            super('shape-editor', context, 'div');

            // Type select
            this._typeSelect = this._makeTypeSelect();
            this.add(this._typeSelect);
            this.element.appendChild(this._typeSelect.element);

            this._forms = {};
            this._onChange = function() {};
            this._onTypeChange = function() {};
        }

        change(field, data) {
            this._onChange(this, data);
        }

        typeChange(type) {
            this._onTypeChange(this, type);
        }

        setShape(shape) {
            let type = shape.shapeType,
                form = this._getTypeForm(type);
            this._typeSelect.setValue(type);
            this._changeType(type);
            form.setObject(shape);
        }

        getShape() {
            let type = this._typeSelect.getValue();
            return this._getTypeForm(type).getObject();
        }

        _makeTypeSelect() {
            let select = new Select.Widget(this.context, {});
            Object.keys(ShapeList).forEach(function(type) {
                select.addOption({name: ShapeList[type].Name, value: type});
            });
            select.onChange = function(input, value) {
                this._changeType(value);
                this.typeChange(value);
            }.bind(this);
            return select;
        }

        _changeType(type) {
            this._hideForms();
            let form = this._getTypeForm(type);
            form.show();
        }

        _getTypeForm(type) {
            if (!this._forms[type]) {
                let config = this._getFormConfig(type),
                    form = new Form.Widget(this.context, config);
                form.setObject(makeShape(type));
                form.addOnChange(this.change.bind(this));
                this._forms[type] = form;
                this.add(form);
                this.element.appendChild(form.element);
            }
            return this._forms[type];
        }

        _getFormConfig(type) {
            let config = this.context.config.Form.Object.shape;
            if (!config) {
                throw "Form config not found for shapes";
            }
            if (!config[type]) {
                throw "Form config not found for `" + type + "' shape";
            }
            return config[type];
        }

        _hideForms() {
            Object.values(this._forms).forEach(function(form) { form.hide(); });
        }

        get onChange() { return this._onChange; }
        set onChange(onChange) { this._onChange = onChange; }

        get onTypeChange() { return this._onTypeChange; }
        set onTypeChange(onTypeChange) { this._onTypeChange = onTypeChange; }
    }

    return {Widget: ShapeEditor};
});
