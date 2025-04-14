define([
    '../../../ui/widgets/menu',
    '../mixin/object-widget',
    '../../commands/add-object'
], function(Base, ObjectWidget, AddObjectCommand) {

    class AddMenu extends ObjectWidget.Mixin(Base.Widget) {
        constructor(context, objectId) {
            super(context);

            this.objectId = objectId

            let typeDesc = this._getTypeDesc(this.objectId.type),
                childTypes = Object.keys(typeDesc.children || {});
            childTypes.forEach(this._addTypeItem, this);
        }

        _addTypeItem(type) {
            let typeDesc = this._getTypeDesc(type),
                name = (typeDesc.name || type);
            this.addItem({
                text: name,
                title: 'Add ' + name,
                action: this._addObject.bind(this, type)
            });
        }

        _addObject(type) {
            let typeDesc = this._getTypeDesc(type);
            if (typeDesc.subtypeSelectDialog) {
                // Create subtype selection dialog
                let dialog = new typeDesc.subtypeSelectDialog(
                    this.context,
                    this.parent.getHeader());
                dialog.onOk = function() {
                    let subtype = dialog.getValue();
                    if (subtype) {
                        // Create selected subtype
                        this._addObjectSubtype(type, subtype);
                    }
                    return true;
                }.bind(this);
                this.add(dialog);
                dialog.show();

            } else if (typeDesc.getSubtypeByParent) {
                // Get subtype by object
                this._addObjectSubtype(type, typeDesc.getSubtypeByParent(this.getObject()));

            } else {
                // Create with no subtype
                this._addObjectSubtype(type);
            }

            // Hide menu
            this.hide();
        }

        _addObjectSubtype(type, subtype = null) {
            let typeDesc = this._getTypeDesc(type)
            if (typeDesc.initDataDialog) {
                // Initial data dialog
                let dialog = new typeDesc.initDataDialog(
                    this.context,
                    this.getObject(),
                    this.parent.getHeader());
                dialog.onOk = function() {
                    let data = dialog.getValue();
                    if (data) {
                        this._addObjectSubtypeCommand(type, subtype, data);
                    }
                }.bind(this);
                this.add(dialog);
                dialog.show();

            } else {
                // Add object
                this._addObjectSubtypeCommand(type, subtype, {});
            }
        }

        _addObjectSubtypeCommand(type, subtype, data = {}) {
            let command = new AddObjectCommand.Command(
                this.context, type, subtype, this.objectId, data);
            command.exec();
            this.context.commandHistory.add(command);
        }

        _getTypeDesc(type) {
            return this.context.objectHelper.getTypeDesc(type);
        }
    }

    return {Widget: AddMenu};
});
