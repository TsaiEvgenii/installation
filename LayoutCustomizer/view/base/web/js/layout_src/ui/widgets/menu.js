define([
    '../widget'
], function(Widget) {

    // [
    //     {text: 'Item 1', action: doSomething, children: [
    //         {text: 'Item 1/1', action: doSomething},
    //         {text: 'Item 1/2', action: doSomething},
    //     ]},
    //     {text: 'Item 2', action: do SomethingElse}
    // ]

    class Item extends Widget.Base {
        constructor(context, root, data) {
            super('menu-item', context, 'li')
            this._root = root; // root menu

            // Menu item
            this._link = this.context.elementFactory.make('a', {
                href: 'javascript:void(0)',
                textContent: data.text || '',
                title: data.title || null,
                onclick: data.action ? this._wrapAction(data.action) : null
            });
            this.element.appendChild(this._link);

            // Submenu
            this._submenu = null;
            (data.children || []).forEach(this.addItem, this);
        }

        addItem(data) {
            this._getSubmenu().addItem(this._root, data);
        }

        _wrapAction(action) {
            let root = this._root;
            return function() {
                action();
                root.hide();
                return false;
            }
        }

        _getSubmenu() {
            if (!this._submenu) {
                let submenu = new Submenu(context, this._root);
                this.add(submenu);
                this.element.appendChild(submenu);
                this._submenu = submenu;
            }
            return this._submenu;
        }
    }

    class MenuBase extends Widget.Base {
        constructor(name, context, root, items = []) {
            super(name, context, 'ul');
            this._root = (root || this);
            items.forEach(this.addItem, this);
        }

        addItem(data) {
            let item = new Item(this.context, this._root, data);
            this.add(item)
            this.element.appendChild(item.element);
        }
    }

    class Submenu extends MenuBase {
        constructor(context, root, items = []) {
            super('submenu', context, root, items);
        }
    }

    class Menu extends MenuBase {
        constructor(context, items = []) {
            super('menu', context, null, items);
            this.hide(); // hidden by default
            this._clickEventListener = this.hide.bind(this);
        }

        show() {
            super.show();
            this._addClickListener();
        }

        hide() {
            super.hide();
            this._removeClickListener();
        }

        destroy() {
            super.destroy();
            this._removeClickListener();
        }

        _addClickListener() {
            this._removeClickListener(); // prevent duplication
            document.addEventListener('click', this._clickEventListener);
        }

        _removeClickListener() {
            document.removeEventListener('click', this._clickEventListener);
        }
    }

    return {Widget: Menu};
});
