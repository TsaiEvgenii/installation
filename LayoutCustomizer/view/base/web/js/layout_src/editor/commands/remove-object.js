define([
    './_object-command'
], function(ObjectCommand) {

    class RemoveObject extends ObjectCommand.Base {
        constructor(context, objectId) {
            super('remove-object', context);

            let oh = this.context.objectHelper;
            let object = this.getObject(objectId),
                parent = oh.getParent(object);
            this._objectId = objectId.copy();
            this._parentId = parent ? parent.objectId.copy() : null;
            this._position = parent ? oh.getPosition(parent, object) : null;
        }

        exec() {
            let object = this.getObject(this._objectId);
            this._removeObject(object);
        }

        undo() {
            let object = this.getObject(this._objectId);
            this._addObject(object, this._parentId, this._position);
        }

        getObjectIds() {
            let ids = [this._objectId];
            if (this._parentId) {
                ids.push(this._parentId);
            }
            return ids;
        }
    }

    return {Command: RemoveObject};
});
