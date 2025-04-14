define([
    '../widget',
    '../../data/helper',
    '../helper/html'
], function(Widget, DataHelper, HtmlHelper) {

    // Parameters:
    // - allowMultiple

    class Base extends Widget.Base {
        constructor(name, context, element, value) {
            super(name, context, element);
            this._container = this.context.elementFactory.make('ul');
        }

        add(child) {
            super.add(child);
            this._container.appendChild(child.element);
        }

        _addChildren(tree, values) {
            values.forEach(this._addChild.bind(this, tree));
        }

        _addChild(tree, value) {
            this.add(new ValueNode(this.context, tree, value));
        }
    }

    class ValueNode extends Base {
        constructor(context, tree, value) {
            super('node', context, 'li');
            this._tree = tree;
            this._value = value;
            this._selected = false;

            // Header
            let header = this.context.elementFactory.make('span');
            this.element.appendChild(header);
            // "expand" button
            this._expandButton = this._makeExpandButton();
            header.appendChild(this._expandButton);
            this._updateExpandButton();

            // label
            this._label = this._makeLabel();
            header.appendChild(this._label);

            // Children
            this.element.appendChild(this._container);
            this._addChildren(this._tree, value.children || []);

            // Collapse by default
            this.collapse();
        }

        _makeLabel() {
            return this._isSelectable()
                ? this._makeClickableLabel()
                : this._makeUnclickableLabel();
        }

        _makeExpandButton() {
            return this.context.elementFactory.make('button', {
                type: 'button',
                onclick: this.toggleExpand.bind(this)
            });
        }

        _makeClickableLabel() {
            return this.context.elementFactory.make('a', {
                'href': 'javascript:void(0)',
                textContent: this._value.label,
                onclick: this.toggle.bind(this)
            });
        }

        _makeUnclickableLabel() {
            return this.context.elementFactory.make('span', {
                textContent: this._value.label
            });
        }

        _isSelectable() {
            return this._value.value !== undefined
                && !this._value.disabled;
        }

        getValue() {
            return this._value.value;
        }

        toggle() {
            this._selected ? this.unselect() : this.select();
        }

        select() {
            if (!this._selected) {
                if (!this._tree.params.allowMultiple) {
                    this._tree.unselectAll();
                }
                this._tree.addValue(this.getValue());
                HtmlHelper.addClassName(this._label, 'selected');
                this._selected = true;
            }
        }

        unselect() {
            if (this._selected) {
                this._tree.removeValue(this.getValue());
                HtmlHelper.removeClassName(this._label, 'selected');
                this._selected = false;
            }
        }

        expand() {
            HtmlHelper.show(this._container);
            this._updateExpandButton();
        }

        collapse() {
            HtmlHelper.hide(this._container);
            this._updateExpandButton();
        }

        toggleExpand() {
            this.isExpanded() ? this.collapse() : this.expand();
        }

        isExpanded() {
            return !HtmlHelper.isHidden(this._container);
        }

        _updateExpandButton() {
            if ((this._value.children || []).length == 0) {
                HtmlHelper.hide(this._expandButton);
            }
            this._expandButton.textContent = (this.isExpanded() ? '-' : '+');
        }
    }

    class ValueTree extends Base {
        constructor(context, params, values) {
            super('value-tree', context, 'div');
            this._element.appendChild(this._container);
            this._params = params;
            this._addChildren(this, values || []);
            this._selectedValues = [];
        }

        addValue(value) {
            this._selectedValues.push(value);
        }

        removeValue(value) {
            let selectedValue = this._selectedValues
                .find(DataHelper.equal.bind(this, value));
            if (selectedValue) {
                let idx = this._selectedValues.indexOf(selectedValue);
                this._selectedValues.splice(idx, 1);
            }
        }

        getValues() {
            return this._selectedValues;
        }

        getValue() {
            return this._selectedValues.length > 0
                ? this._selectedValues[0]
                : 0;
        }

        unselectAll() {
            function unselectNode(node) {
                node.unselect();
                node.children.forEach(unselectNode);
            }
            this.children.forEach(unselectNode);
            this._selectedValues = [];
        }

        get params() { return this._params; }
    }

    return {Widget: ValueTree}
});
