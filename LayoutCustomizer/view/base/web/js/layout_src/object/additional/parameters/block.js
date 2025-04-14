define([
    '../parameter'
], function(Parameter) {

    class BlockParameter extends Parameter.Base {
        constructor() {
            super('block');
        }
    }

    return BlockParameter;
});
