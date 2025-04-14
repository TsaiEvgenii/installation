define([
    '../../blocks/block',
    '../../ui/widget',
    '../../data/helper',
    './object-tree/object-node',
    '../commands/add-object'
], function(Block, Widget, DataHelper, ObjectNode, AddObjectCommand) {

    class ObjectTree extends Widget.Base {
        constructor(context) {
            super('object-tree', context, 'div');

            let om = context.objectManager,
                em = context.eventManager,
                ef = context.elementFactory

            // node container
            this._container = ef.make('ul');
            this._element.appendChild(this._container);


            // "Add Root Block" button
            let addButton = ef.make(
                'button', {
                    type: 'button',
                    className: 'add',
                    textContent: 'Add Root Block'
                });
            addButton.onclick = this._addRootBlock.bind(this);
            this._element.appendChild(addButton);

            // subscribe
            em.subscribe(this, 'object', 'added');
        }

        add(widget) {
            super.add(widget);
            this._container.append(widget.element);
        }

        onEvent(event) {
            if (event.type == 'object'
                && event.name == 'added'
                && event.data.parentId === null)
            {
                this.add(new ObjectNode.Widget(this.context, event.data.id));
                this.update();
            }
        }

        _addRootBlock() {
            let command = new AddObjectCommand.Command(this.context, 'block', null);
            command.exec();
            this.context.commandHistory.add(command);
        }

        _getRootBlockDefaults() {
            let config = this.context.config.Object.Defaults;
            return config['_rootBlock'] || {};
        }
    }

    return {Widget: ObjectTree};
});
