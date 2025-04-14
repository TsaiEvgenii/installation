define([
    '../restriction'
], function(Restriction) {

    class BlockRestriction extends Restriction.Base {
        constructor() {
            super('block');
            this.params.minWidth = null;
            this.params.minHeight = null;
            this.params.maxWidth = null;
            this.params.maxHeight = null;
        }
    }

    return BlockRestriction;
});
