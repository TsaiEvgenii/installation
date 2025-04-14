define([
    '../../../ui/widget'
], function(Widget) {

    let ObjectWidget = Base => class extends Base {
        getObject() {
            if (!this._objectId) {
                throw "Object ID is not set";
            }
            return this.context.objectManager.get(this._objectId);
        }

        objectExists() {
            return this._objectId
                ? this.context.objectManager.has(this._objectId)
                : false;
        }

        get objectId() { return this._objectId.copy(); }
        set objectId(id) { this._objectId = id.copy(); }
    }

    return {Mixin: ObjectWidget};
});
