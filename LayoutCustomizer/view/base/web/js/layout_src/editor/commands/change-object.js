define([
    './_object-command',
    '../../data/helper'
], function(ObjectCommand, DataHelper) {

    class ChangeObject extends ObjectCommand.Base {
        constructor(context, objectId, data) {
            super('change-object', context);

            let object = this.getObject(objectId);
            this._objectId = objectId.copy();
            this._data = DataHelper.copy(data);
            this._oldData = DataHelper.copy(
                DataHelper.getFields(object, Object.keys(data)));

            //if object child need to be reversed
            if(this._data && this._data.reverse && this._data.reverse == 'reverse') {
                if(this._oldData.reverse == 'normal' || this._oldData.reverse == '') {
                    object._isReversed = false;
                }
            }
        }

        exec() {
            this._updateObject(this._data);
        }

        undo() {
            this._updateObject(this._oldData);
        }

        combine(other) {
            this._data = DataHelper.copy(other._data);
            return DataHelper.equal(this._data, this._oldData);
        }

        _canCombine(other) {
            // NOTE: for now allowing combine commands with single data field
            // if both values are not null or object, counting toggling nullable field
            // as separate command
            let data = this._data,
                keys = Object.keys(data),
                dataOther = other._data,
                keysOther = Object.keys(dataOther)
            if (keys.length == 1 && keysOther.length == 1) {
                function canCombineValue(value) {
                    return value !== null
                        && typeof value != 'object';
                }
                let key = keys[0],
                    keyOther = keysOther[0];
                if (key == keyOther
                    && canCombineValue(data[key])
                    && canCombineValue(dataOther[key]))
                {
                    return true;
                }
            }
            return false;
        }

        getObjectIds() {
            return [this._objectId];
        }

        _updateObject(data) {
            let object = this.getObject(this._objectId);
            // update object data
            DataHelper.setFields(object, data);
            // notify
            this.context.eventManager.notify('object', 'changed', {
                id: this._objectId,
                data: data
            });
        }
    }

    return {Command: ChangeObject};
});
