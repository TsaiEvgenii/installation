define([
    '../../data/helper',
    './_object-command'
], function(DataHelper, ObjectCommand) {

    class AddObject extends ObjectCommand.Base {
        constructor(context, type, subtype, parentId = null, data = {}) {
            super('add-object', context);
            this._type = type;
            this._subtype = subtype;
            this._parentId = parentId ? parentId.copy() : null;
            this._objectId = null;
            this._data = DataHelper.copy(data);
        }

        exec() {
            let object = this._getOrMakeObject();
            this._addObject(object, this._parentId);
        }

        undo() {
            let object = this.getObject(this._objectId)
            this._removeObject(object, this._parentId);
        }

        getObjectIds() {
            let ids = [this._objectId];
            if (this._parentId) {
                ids.push(this._parentId);
            }
            return ids;
        }

        _getOrMakeObject() {
            let object = null;
            if (this._objectId) {
                object = this.getObject(this._objectId);
            } else {
                object = this.context.objectManager.make(this._type, this._subtype);
                this._objectId = object.objectId.copy();

                let objectConfig = this.context.config.Object,
                    isRootBlock = (!this._parentId && this._type == 'block');

                // Set default values for root block
                if (isRootBlock) {
                    if (objectConfig.Defaults && objectConfig.Defaults._rootBlock) {
                        let defaults = objectConfig.Defaults._rootBlock
                        DataHelper.setFields(object, defaults);
                    }
                }

                // Set data
                DataHelper.setFields(object, this._data);


                // Run `create' hooks
                let hooks = objectConfig.Hooks;
                if (hooks[this._type] && hooks[this._type].create) {
                    hooks[this._type].create(this.context, object);
                }
                // hooks for root block
                if (isRootBlock && hooks._rootBlock && hooks._rootBlock.create) {
                    hooks._rootBlock.create(this.context, object);
                }
            }
            return object;
        }
    }

    return {Command: AddObject};
});
