define([
    '../dialog',
    '../../helper/html'
], function(Dialog, HtmlHelper) {

    class SelectList extends Dialog.Base {
        constructor(name, context, params, container = null) {
            super('select-list-dialog ' + name, context, container);
            this._params = params;

            this._list = this.context.elementFactory.make('ul');
            this.body.appendChild(this._list);
            this._value = null;
            this._items = [];
        }

        addItem(text, value) {
            let item = this.context.elementFactory.make('li', {
                textContent: text,
            });
            // item.onclick = this._onClick.bind(this, item, value);
            item.onclick = function(event) {
                event.stopPropagation();
                event.preventDefault();
                this._select(item, value);
            }.bind(this);
            this._items.push(item);
            this._list.appendChild(item);
        }

        getValue() {
            return this._value;
        }

        _select(item, value) {
            // set value
            this._value = value;
            // assign "selected" class
            this._items.forEach(function(item) {
                HtmlHelper.removeClassName(item, 'selected');
            });
            HtmlHelper.addClassName(item, 'selected');
        }
    }

    return {Widget: SelectList};
});
