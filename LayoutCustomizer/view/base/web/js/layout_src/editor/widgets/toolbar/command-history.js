define([
    '../../../ui/widget'
], function(Widget) {

    let Type = 'command-history',
        Name = 'Command History';

    class CommandHistory extends Widget.Base {
        constructor(context) {
            super('command-history', context, 'div');

            let ef = this.context.elementFactory,
                history = this.context.commandHistory;

            // Undo button
            this._undoButton = ef.make('button', {
                type: 'button',
                'className': 'undo',
                innerHTML: '&larr;',
                title: 'Undo',
                onclick: this._undo.bind(this)
            });
            this.element.appendChild(this._undoButton);

            // Redo button
            this._redoButton = ef.make('button', {
                type: 'button',
                'className': 'redo',
                innerHTML: '&rarr;',
                title: 'Redo',
                onclick: this._redo.bind(this)
            });
            this.element.appendChild(this._redoButton);

            // Add command history callback
            this._historyUpdateHanler = this._update.bind(this);
            this.context.commandHistory.addAfterAddHandler(this._historyUpdateHanler);
            this.context.commandHistory.addAfterRemoveHandler(this._historyUpdateHanler);

            // Update
            this._update();
        }

        destroy() {
            super.destroy();
            this.context.commandHistory.removeAfterAddHandler(this._afterAddHandler);
            this.context.commandHistory.removeAfterRemoveHandler(this._afterAddHandler);
        }

        _update() {
            let history = this.context.commandHistory;
            this._undoButton.disabled = !history.canUndo();
            this._redoButton.disabled = !history.canRedo();
        }

        _undo() {
            this.context.commandHistory.undo();
            this._update();
        }

        _redo() {
            this.context.commandHistory.redo()
            this._update();
        }
    }

    return {
        Type: Type,
        Name: Name,
        Widget: CommandHistory
    };
});
