define([
    'require',
    '../../../ui/widgets/field',
    '../../commands/change-object'
], function(require, Field, ChangeObjectCommand) {

    class ShapeEditor extends Field.Base {
        constructor(context, params) {
            super('shape-editor', context, params, 'div');
            this._shape = null;
            // Add editor
            this._editor = null;
            require(['../shape-editor'], function(ShapeEditor) {
                this._editor = new ShapeEditor.Widget(this.context);
                this._editor.onChange = this.onEditorChange.bind(this);
                this._editor.onTypeChange = this.onEditorTypeChange.bind(this);
                this.element.appendChild(this._editor.element);
                if (this._shape) {
                    this._editor.setShape(this._shape);
                }
            }.bind(this));
        }

        onEditorTypeChange(editor, type) {
            this._shape = editor.getShape();
            this.change();
        }

        onEditorChange(editor, data) {
            this._shape = editor.getShape();

            // Modifying object directly, without notifying form
            // TODO: refactoring

            let field = this.parent,
                object = field.form.getObject(),
                objectData = {};
            for (let key in data) {
                objectData[field.name + '.' + key] = data[key];
            }
            // Run object change command
            let command = new ChangeObjectCommand.Command(
                this.context, object.objectId, objectData);
            command.exec();
            this.context.commandHistory.add(command);
        }

        setValue(shape) {
            this._shape = shape;
            if (this._editor) {
                this._editor.setShape(this._shape);
            }
        }

        getValue(shape) {
            return this._shape;
        }

        reset() {
            // do nothing
        }
    }

    return {Widget: ShapeEditor};
});
