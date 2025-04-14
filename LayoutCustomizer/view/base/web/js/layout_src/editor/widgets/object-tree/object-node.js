define([
    '../../../ui/widget',
    '../../../ui/helper/html',
    '../mixin/object-widget',
    './dnd',
    '../../commands/remove-object',
    './add-menu'
], function(
    Widget, HtmlHelper, ObjectWidget, Dnd,
    RemoveObjectCommand, AddMenu) {

    class ObjectNode extends ObjectWidget.Mixin(Widget.Base) {
        constructor(context, objectId) {
            super('node', context, 'li');
            this.objectId = objectId;

            // Add type class
            HtmlHelper.addClassName(this.element, this.objectId.type + '-node');

            // header
            this._label = this._makeLabel();
            this._addButton = this._makeAddButton();
            this._removeButton = this._makeRemoveButton();
            this._header = this._makeHeader(this._label, this._addButton, this._removeButton);
            this.element.appendChild(this._header);

            // subscribe to object events
            context.eventManager.subscribe(this, 'object', 'all');

            // check if selected
            this._updateSelected();
            // set position
            this._updatePosition();

            this._addMenu = null;

            // child lists
            let oh = this.context.objectHelper;
            this._objectLists = {};
            let typeDesc = oh.getTypeDesc(this.objectId.type);
            Object.keys(typeDesc.children).forEach(function(childType) {
                this._addObjectList(childType);
            }, this);

            // Drag and drop
            this._dnd = new Dnd(this);
        }

        getLabel() {
            return this._label;
        }

        getHeader() {
            return this._header;
        }

        getAddButton() {
            return this._addButton;
        }

        getDeleteButton() {
            return this._deleteButton;
        }

        getObjectLists() {
            return this._objectLists;
        }

        getObjectList(type) {
            return this._objectLists[type];
        }

        onEvent(event) {
            if (event.type == 'object') {
                switch (event.name) {
                case 'changed':
                    if (this.objectId.isSame(event.data.id)) {
                        this._updateLabel();
                    }
                    break;
                case 'added':
                    if (this.objectId.isSame(event.data.parentId)) {
                        this._addNode(event.data.id);
                    }
                    break;
                case 'removed':
                    if (this.objectId.isSame(event.data.id)) {
                        this.destroy();
                    }
                    break;
                case 'selected':
                    this._updateSelected();
                    break;
                }
            }
        }

        _update() {
            this._updateSelected();
            this._updatePosition();
        }

        _addNode(objectId) {
            let node = new ObjectNode(this.context, objectId),
                list = this._objectLists[objectId.type];
            if (node && list) {
                list.appendChild(node.element);
                this.add(node);
                this.update();
            }
        }

        _addObjectList(type) {
            let ef = this.context.elementFactory,
                oh = this.context.objectHelper;
            // Container, list
            let container = ef.make('div', {className: 'child-container'}),
                list = ef.make('ul');
            HtmlHelper.addClassName(container, type + '-container');
            container.appendChild(list);
            this.element.appendChild(container);

            // Add to lists
            this._objectLists[type] = list;
        }

        _makeHeader(label, addButton, removeButton) {
            // TODO: refactoring
            let oh = this.context.objectHelper,
                typeDesc = oh.getTypeDesc(this.objectId.type),
                header = this.context.elementFactory.make('label');
            header.appendChild(label);
            if (Object.keys(typeDesc.children || {}).length > 0) {
                header.appendChild(addButton);
            }
            header.appendChild(removeButton);
            return header;
        }

        _makeLabel() {
            let oh = this.context.objectHelper;
            return this.context.elementFactory.make('a', {
                href: 'javascript:void(0)',
                textContent: oh.getName(this.getObject()),
                onclick: this._selectObject.bind(this)
            });
        }

        _updateLabel() {
            let oh = this.context.objectHelper;
            this._label.textContent = oh.getName(this.getObject());
        }

        _makeAddButton() {
            return this.context.elementFactory.make('button', {
                type: 'button',
                className: 'add',
                textContent: '+',
                onclick: this._addObjectMenu.bind(this)
            });
        }

        _makeRemoveButton() {
            return this.context.elementFactory.make('button', {
                type: 'button',
                className: 'remove',
                textContent: 'X',
                onclick: this._removeObject.bind(this)
            });
        }

        _selectObject() {
            this.context.eventManager
                .notify('object', 'selected', {id: this.objectId});
        }

        _addObjectMenu(event) {
            let menu = this._getAddObjectMenu();
            if (!menu.isVisible()) {
                event.stopPropagation();
                menu.show();
            }
        }

        _getAddObjectMenu() {
            if (!this._addMenu) {
                let menu = new AddMenu.Widget(this.context, this.objectId);
                this.add(menu);
                this._header.appendChild(menu.element);
                this._addMenu = menu;
            }
            return this._addMenu;
        }

        _removeObject() {
            let command = new RemoveObjectCommand.Command(this.context, this.objectId);
            command.exec();
            this.context.commandHistory.add(command);
        }

        _updateSelected() {
            let selected = this.context.selectedObjectIds.has(this.objectId),
                func = selected ? HtmlHelper.addClassName : HtmlHelper.removeClassName;
            func(this._label, 'selected');
        }

        _updatePosition() {
            let oh = this.context.objectHelper;
            let object = this.getObject(),
                parent = oh.getParent(object);
            let order = parent
                ? oh.getPosition(parent, object)
                : this.context.rootObjectIds.position(object.objectId);
            this.element.style.order = order;
        }
    }

    return {Widget: ObjectNode};
});
