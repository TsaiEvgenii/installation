define([
    '../../ui/widgets/form',
    './fields/all'
], function(Base, FieldTypeList) {

    class FormBase extends Base.Base {
        constructor(context, formConfig) {
            super(context, FieldTypeList, formConfig);
        }
    }

    class Form extends FormBase {
        constructor(context, formConfig) {
            super(context, formConfig);
            this._object = null;
        }

        hasObject() {
            return this._object !== null;
        }

        getObject() {
            return this._object;
        }

        setObject(object) {
            this._object = object;
            this.reset();
            this.update();
        }
    }

    return {
        Base: FormBase,
        Widget: Form
    };
});
