define([
    '../object/id'
], function(ObjectId) {

    class CommandBase {
        constructor(type, context) {
            this._type = type;
            this._context = context;
            this._canUndo = false;
        }

        exec() {}
        undo() {}

        canCombine(other) {
            return (other.type == this._type)
                && this._canCombine(other);
        }

        _canCombine(other) {
            return false;
        }

        combine(other) {}

        canUndo() { this._canUndo; }

        getObjectIds() { return []; }

        get context() { return this._context; }
        get type() { return this._type; }
    }

    class History {
        constructor(limit) {
            this._currentIdx = -1;
            this._commands = [];
            this._limit = limit;
            this._afterAddHandlers = [];
            this._afterRemoveHandlers = []
            this._afterClearHandlers = [];
        }

        add(command) {
            // Remove all history after current command
            if (this._currentIdx < this._commands.length - 1) {
                this._commands.splice(this._currentIdx + 1);
            }

            // Add new or combine with previous
            let last = (this._commands.length > 0)
                ? this._commands[this._commands.length - 1]
                : null;
            if (last && last.canCombine(command)) {
                if (last.combine(command)) {
                    // last command is reversed by update, remove
                    this.remove();
                }
            } else {
                this._commands.push(command);
                this.clear();
            }

            // Point to new command
            this._currentIdx = this._commands.length - 1;

            this._afterAddHandlers.forEach(function(handler) {
                handler(this, command);
            });
        }

        remove() {
            let command = this._commands.pop();
            this._afterRemoveHandlers.forEach(function(handler) {
                handler(this, command);
            });
        }

        clear() {
            let num = this._commands.length;
            if (num > this._limit) {
                this._commands.splice(0, num - this._limit);
                this._afterClearHandlers.forEach(function(handler) {
                    handler(this);
                });
            }
        }

        current() {
            return (this._currentIdx > -1)
                ? this._commands[this._currentIdx]
                : null;
        }

        undo() {
            if (this.canUndo()) {
                this._commands[this._currentIdx--].undo();
            }
        }

        redo() {
            if (this.canRedo()) {
                this._commands[++this._currentIdx].exec();
            }
        }

        canUndo() {
            return this._currentIdx > -1;
        }

        canRedo() {
            return this._currentIdx < this._commands.length - 1;
        }

        getObjectIds() {
            // A set of all used object IDs
            return this._commands.reduce(function(set, command) {
                command.getObjectIds().forEach(set.add, set);
                return set;
            }, new ObjectId.Set());
        }

        addAfterAddHandler(handler) {
            this._afterAddHandlers.push(handler);
        }

        removeAfterAddHandler(handler) {
            this._removeHandler(this._afterAddHandlers, handler);
        }

        addAfterRemoveHandler(handler) {
            this._afterRemoveHandlers.push(handler);
        }

        removeAfterRemoveHandler(handler) {
            this._removeHandler(this._afterRemoveHandlers, handler);
        }

        addAfterClearHandler(handler) {
            this._afterClearHandlers.push(handler);
        }

        removeAfterClearHandler(handler) {
            this._removeHandler(this._afterClearHandlers, handler);
        }

        _removeHandler(list, handler) {
            let idx = list.indexOf(handler);
            if (idx != -1) {
                list.splice(idx, 1);
            }
        }
    }

    return {
        Base: CommandBase,
        History: History
    };
});
