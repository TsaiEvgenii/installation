define([
    '../../ui/widget',
    '../../data/helper'
], function(Widget, DataHelper) {

    // Input params:
    // - onChange: function(input, newValue) {}
    // - isNullable: bool
    // - nullText: string
    // - input: {} -- input element attributes
    // - options: [] -- select options

    class Base extends Widget.Base {
        constructor(name, context, params, elementType, elementAttributes) {
            super('field ' + name, context, elementType, elementAttributes);
            this._params = params;
            this._onChange = function() {};
            this._input = null;

            if (params.onChange) {
                this._onChange = params.onChange;
            }
        }

        getValue() { return this._input.value; }
        setValue(value) { this._input.value = value; }

        validate() {
            return this._validate(this.getValue());
        }

        change() {
            if (this.validate()) {
                this._onChange(this, this.getValue());
            }
        }

        reset() {
            this.setValue('');
        }

        _validate() { return true; }

        get input() { return this._input; }

        get params() { return this._params; }

        set onChange(onChange) { this._onChange = onChange; }
    }

    class InputBase extends Base {
        constructor(name, context, type, params) {
            super('input ' + name, context, params, 'div');

            // Input element
            let ef = context.elementFactory,
                attributes = Object.assign(
                    {type: type, id: true}, // auto-generate id
                    params.input || {});
            this._input = ef.make('input', attributes)
            this._element.appendChild(this._input);

            // Init element(s)
            this.init();
        }

        init() {
            this._input.onchange = this.change.bind(this);
        }
    }

    class NullableInputBase extends InputBase {
        constructor(name, context, type, params) {
            super('nullable ' + name, context, type, params);

            this._nullCheckbox = null;
            if (params.isNullable) {
                // Create checkbox
                let labelText = (params.nullText || params.nullText == '')
                    ? params.nullText
                    : 'NULL';
                let ef = context.elementFactory,
                    label = ef.make('label', {textContent: labelText}),
                    checkbox = ef.make('input', {type: 'checkbox'});
                label.appendChild(checkbox);
                this._element.appendChild(label);
                // Init checkbox
                this._nullCheckbox = checkbox;
                this._nullCheckbox.onclick = this.change.bind(this);
            }
        }

        getValue() {
            let checkbox = this._nullCheckbox;
            return (checkbox && checkbox.checked)
                ? null
                : super.getValue();
        }

        setValue(value) {
            let checkbox = this._nullCheckbox,
                valueIsNull = (value === null);
            if (checkbox) {
                checkbox.checked = valueIsNull;
                this._input.disabled = valueIsNull;
                if (!valueIsNull) {
                    this._input.value = value;
                }
            } else {
                this._input.value = valueIsNull ? '' : value;
            }
        }

        change() {
            this._input.disabled = (this.getValue() === null)
            super.change();
        }

        reset() {
            super.reset();
            let checkbox = this._nullCheckbox;
            if (checkbox) {
                checkbox.checked = true;
                this._input.disabled = true;
            }
        }

        isNullable() { return !!this._nullCheckbox; }

        get nullCheckbox() { return this._nullCheckbox; }
    }

    return {
        Base: Base,
        InputBase: InputBase,
        NullableInputBase: NullableInputBase
    };
})
