define([
    '../../../object/id',
    '../../commands/move-object',
    '../../helper/dnd',
    '../../../ui/helper/html'
], function(ObjectId, MoveObjectCommand, DndHelper, HtmlHelper) {

    // Helper functions

    function removeTargetClassNames(element) {
        ['drop-before', 'drop-after'].forEach(
            HtmlHelper.removeClassName.bind(null, element));
        let lists = element.getElementsByClassName('drop-into');
        for (let i = 0; i < lists.length; ++i) {
            HtmlHelper.removeClassName(lists[i], 'drop-into');
        }
    }

    function setCanMove(event, canMove) {
        event.dataTransfer.effectAllowed = (canMove ? 'move' : 'none');
    }

    // event.dataTransfer data is not accessible in Chrome
    let draggedIdString = null;

    function setDraggedObjectId(event, objectId) {
        // event.dataTransfer.setData('text', objectId.toString());
        draggedIdString = objectId.toString();
    }

    function getDraggedObjectId(event) {
        // let idString = event.dataTransfer.getData('text');
        let idString = draggedIdString;
        return ObjectId.Id.fromString(idString);
    }


    class Dnd {
        constructor(node) {
            this._context = node.context;
            this._node = node;
            this._initNode(this._node);
        }

        _initNode(node) {
            let label = node.getLabel(),
                addButton = node.getAddButton();
            label.draggable = true;

            // Dragging
            label.ondragstart = this.dragStart.bind(this);
            label.ondragend = this.dragEnd.bind(this);

            // Sorting
            label.ondragover = this.dragOverLabel.bind(this);
            label.ondragleave = this.dragLeaveLabel.bind(this);
            label.ondrop = this.dropLabel.bind(this);

            // Adding
            addButton.ondragover = this.dragOverAddButton.bind(this);
            addButton.ondragleave = this.dragLeaveAddButton.bind(this);
            addButton.ondrop = this.dropAddButton.bind(this);
        }

        getObject(objectId) {
            return this._context.objectManager.get(objectId);
        }

        getDraggedObject(event) {
            return this.getObject(getDraggedObjectId(event));
        }

        dragStart(event) {
            HtmlHelper.addClassName(this._node.element, 'dragged');
            setCanMove(event, true);
            setDraggedObjectId(event, this._node.objectId);
        }

        dragEnd(event) {
            HtmlHelper.removeClassName(this._node.element, 'dragged');
        }

        _dragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            setCanMove(event, false);
            removeTargetClassNames(this._node.element);
        }

        dragOverLabel(event) {
            this._dragOver(event);
            let oh = this._context.objectHelper,
                draggedObject = this.getDraggedObject(event),
                targetObject = this._node.getObject();
            if (oh.getType(draggedObject) == oh.getType(targetObject)) {
                let parent = oh.getParent(targetObject);
                // TODO: check if dragged object can be root
                if (!parent || oh.canAddChild(parent, draggedObject)) {
                    setCanMove(event, true);
                    HtmlHelper.addClassName(this._node.element, 'drop-before');
                }
            }
        }

        dragOverAddButton(event) {
            this._dragOver(event);
            let oh = this._context.objectHelper,
                draggedObject = this.getDraggedObject(event),
                targetObject = this._node.getObject();
            if (oh.canAddChild(targetObject, draggedObject)) {
                setCanMove(event, true);
                let type = oh.getType(draggedObject),
                    list = this._node.getObjectList(type);
                HtmlHelper.addClassName(list, 'drop-into');
            }
        }

        _dragLeave(event) {
            event.preventDefault();
            event.stopPropagation();
            setCanMove(event, false);
            removeTargetClassNames(this._node.element);
        }

        dragLeaveLabel(event) {
            this._dragLeave(event);
        }

        dragLeaveAddButton(event) {
            this._dragLeave(event);
        }

        _drop(event) {
            event.preventDefault();
            event.stopPropagation();
            removeTargetClassNames(this._node.element);
        }

        dropLabel(event) {
            this._drop(event);
            let oh = this._context.objectHelper,
                draggedObject = this.getDraggedObject(event),
                targetObject = this._node.getObject(),
                targetParent = oh.getParent(targetObject);
            let position = targetParent
                ? oh.getPosition(targetParent, targetObject)
                : this._context.rootObjectIds.position(targetObject.objectId);
            this._moveInto(targetParent, draggedObject, position);
        }

        dropAddButton(event) {
            this._drop(event);
            let oh = this._context.objectHelper,
                draggedObject = this.getDraggedObject(event),
                targetObject = this._node.getObject();
            if (oh.canAddChild(targetObject, draggedObject)) {
                this._moveInto(targetObject, draggedObject);
            }
        }

        _moveInto(parent, object, position = null) {
            let parentId = parent ? parent.objectId : null;
            let command = new MoveObjectCommand.Command(
                this._context, object.objectId, parentId, position);
            command.exec();
            this._context.commandHistory.add(command);
        }
    }

    return Dnd;
});
