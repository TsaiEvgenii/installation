define([
    '../form',
    '../mixin/object-widget',
    '../../commands/change-object'
], function(ObjectForm, ObjectWidget, ChangeObjectCommand) {

    class Form extends ObjectWidget.Mixin(ObjectForm.Base) {
        constructor(context, config) {
            super(context, config);
            // subscribe to object events
            this.context.eventManager.subscribe(this, 'object');
        }

        onEvent(event) {
            if (event.type == 'object'
                && this.objectId
                && this.objectId.isSame(event.data.id))
            {
                switch (event.name) {
                case 'removed':
                    this.hide();
                    break;
                case 'changed':
                    this.reset();
                    break;
                }
            }
        }

        _change(field, data) {
            let object = this.getObject();
            if (object) {
                let command = new ChangeObjectCommand.Command(
                    this.context, this.objectId, data);
                command.exec();
                this.context.commandHistory.add(command);
            }
        }

        _update() {
            if (this.objectId && !this.objectExists(this.objectId)) {
                this.objectId = null;
            } else {
                this.reset();
            }
        }

        // getObject() added by mixin
        hasObject() { return this.objectExists(); }

        get objectId() { return this._objectId; }
        set objectId(objectId) { this._objectId = objectId; this.reset(); }
    }

    return {Widget: Form};
});
