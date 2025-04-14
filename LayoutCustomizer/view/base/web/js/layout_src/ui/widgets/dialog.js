define([
    '../widget'
], function(Widget) {

    function pxIfNumber(value) {
        return isNaN(Number(value))
            ? value.toString()
            : value.toString() + 'px';
    }

    class Dialog extends Widget.Base {
        constructor(name, context, container = null) {
            super('dialog ' + name, context, 'div');
            this.hide(); // dialogs are hidden by default

            // Add main elements
            let ef = this.context.elementFactory;
            // make
            this._header = ef.make('div', {className: 'dialog-header'});
            this._body = ef.make('div', {className: 'dialog-body'});
            this._buttonset = ef.make('div', {className: 'dialog-buttonset'});
            // hide header and buttons
            this._header.style.display = 'none';
            this._buttonset.style.display = 'none';
            // append
            this.element.appendChild(this._header);
            this.element.appendChild(this._body);
            this.element.appendChild(this._buttonset);

            // buttons by name
            this._buttons = {};

            // default Ok/Cancel handlers
            this._onOkCustom = function() { return true; };
            this._onCancelCustom = function() { return true; };

            // add to body element
            (container || this._context.rootElement).appendChild(this.element);
        }

        setSize(width, height) {
            if (width !== null) {
                this._element.style.width = pxIfNumber(width);
            }
            if (height !== null) {
                this._element.style.height = pxIfNumber(height);
            }
        }

        setPosition(left, top) {
            this._element.style.left = pxIfNumber(left);
            this._element.style.top = pxIfNumber(top);
        }

        center() {
            this.setPosition('50%', '50%');
        }

        setHeaderText(text) {
            this._header.textContent = text;
            this._header.style.display = ''; // show header
        }

        addOkButton() {
            this.addButton('ok', {
                className: 'ok',
                textContent: 'Ok',
                onclick: this._onOk.bind(this)
            });
        }

        addCancelButton() {
            this.addButton('cancel', {
                className: 'cancel',
                textContent: 'Cancel',
                onclick: this._onCancel.bind(this)
            });
        }

        addButton(name, attributes) {
            if (this._buttons[name]) {
                throw "Button `" + name  + "' already exists";
            }
            let button = this.context.elementFactory.make('button', attributes);
            this._buttonset.appendChild(button);
            this._buttons[name] = button;
            this._buttonset.style.display = ''; // show buttonset
        }

        _onOk() {
            if (this._onOkCustom(this) !== false) {
                this.destroy();
            }
        }

        _onCancel() {
            if (this._onCancelCustom(this) !== false) {
                this.destroy();
            }
        }

        getButton(name) {
            return this._buttons[name];
        }

        get body() { return this._body; }

        set onOk(onOk) { this._onOkCustom = onOk; }
        set onCancel(onCancel) { this._onCancelCustom = onCancel; }
    }

    return {Base: Dialog};
});
