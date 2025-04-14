define([
    './_object-command'
], function(ObjectCommand) {

    class MoveObject extends ObjectCommand.Base {
        constructor(context, objectId, newParentId = null, newPosition = null) {
            super('move-object', context);

            let oh = this.context.objectHelper;
            let object = this.getObject(objectId),
                parent = oh.getParent(object);
            this._objectId = objectId.copy();
            this._oldParentId = parent ? parent.objectId.copy() : null;
            this._oldPosition = parent
                ? oh.getPosition(parent, object)
                : this.context.rootObjectIds.position(objectId);
            this._newParentId = newParentId ? newParentId.copy() : null;
            this._newPosition = newPosition;
        }

        exec() {
            let object = this.getObject(this._objectId);
            this._moveObject(object, this._newParentId, this._newPosition);
        }

        undo() {
            let object = this.getObject(this._objectId);
            this._moveObject(object, this._oldParentId, this._oldPosition);
        }

        getObjectIds() {
            let ids = [this._objectId]
            if (this._oldParentId) {
                ids.push(this._oldParentId);
            }
            if (this._newParentId) {
                ids.push(this._newParentId);
            }
            return ids;
        }
    }

    return {Command: MoveObject};
});
