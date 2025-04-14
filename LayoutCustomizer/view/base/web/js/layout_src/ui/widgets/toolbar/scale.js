define([
    '../../widget',
    '../fields/number',
    '../fields/select'
], function(Widget, NumberField, SelectField) {

    let Type = 'scale',
        Name = 'Scale';

    let CustomValue = 'custom';

    class Scale extends Widget.Base {
        constructor(context, selectOptions) {
            super('scale', context, 'div');

            // Scale select
            selectOptions.push({name: 'Custom', value: CustomValue});
            this._select = new SelectField.Widget(context, {
                options: selectOptions,
                onChange: this.changeSelect.bind(this)
            });
            this.add(this._select);

            // Custom scale input
            this._field = new NumberField.Widget(context, {
                input: {step: 0.01},
                onChange: this.changeInput.bind(this)
            });
            this.add(this._field);

            this.element.appendChild(this._select.element);
            this.element.appendChild(this._field.element);

            this._initValue();
        }

        _initValue() {
            let value = this.context.scale;
            this._field.setValue(value);
            if (this._select.hasValue(value)) {
                this._select.setValue(value);
                this._field.hide();
            } else {
                this._select.setValue(CustomValue);
                this._field.show();
            }
        }

        changeSelect(input, value) {
            if (value == CustomValue) {
                this._field.setValue(this.context.scale);
                this._field.show();
            } else {
                this._field.hide();
                this._updateContext(value);
            }
        }

        changeInput(input, value) {
            this._updateContext(value);
        }

        _updateContext(value) {
            this.context.scale = value;
            this.context.eventManager.notify('context', 'changed', {
                scale: value
            });
        }
    }

    return {
        Type: Type,
        Name: Name,
        Widget: Scale
    };
});
