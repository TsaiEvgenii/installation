define([
    '../command',
], function(Command) {

    class ObjectCommand extends Command.Base {
        getObject(objectId) {
            return this.context.objectManager.get(objectId);
        }

        _addObject(object, parentId, position) {
            let oh = this.context.objectHelper;
            if (parentId) {
                // Add to parent
                let parent = this.getObject(parentId);
                oh.insertChild(parent, object, position);
            } else {
                // Add root object
                this.context.rootObjectIds.insert(object.objectId, position);
            }

            // Restore object
            let om = this.context.objectManager;
            oh.forEach(object, function(child) {
                om.restore(child.objectId);
            });

            // Send event
            let em = this.context.eventManager;
            oh.forEach(object, function(child) {
                em.notify('object', 'added', {
                    id: child.objectId,
                    parentId: oh.getParentId(child)
                });
            });
        }

        _removeObject(object) {
            let oh = this.context.objectHelper,
                parentId = oh.getParentId(object);
            if (parentId) {
                // Remove from parent
                let parent = this.getObject(parentId);
                oh.removeChild(parent, object);
            } else {
                // Remove from root list
                this.context.rootObjectIds.remove(object.objectId);
            }

            // Remove object
            let om = this.context.objectManager;
            oh.forEach(object, function(child) {
                om.remove(child.objectId);
            });

            // Send event
            this.context.eventManager.notify('object', 'removed', {
                id: object.objectId,
                parentId: parentId
            });
        }

        _moveObject(object, newParentId, newPosition) {
            let oh = this.context.objectHelper;
            let oldParent = oh.getParent(object),
                oldParentId = oldParent ? oldParent.objectId.copy() : null;
            let oldPosition = oldParent
                ? oh.getPosition(oldParent, object)
                : this.context.rootObjectIds.position(object.objectId);
            let newParent = newParentId ? this.getObject(newParentId) : null;
            // Adjust position
            if (oldParent == newParent
                && oldPosition !== null
                && newPosition !== null
                && newPosition > oldPosition
                && newPosition > 0)
            {
                --newPosition;
            }
            this._removeObject(object)
            this._addObject(object, newParentId, newPosition);
        }
    }

    return {Base: ObjectCommand};
})
