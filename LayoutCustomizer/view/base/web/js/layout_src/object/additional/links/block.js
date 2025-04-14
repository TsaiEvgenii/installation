define([
    '../link'
], function(Link) {

    class BlockLink extends Link.Base {
        constructor() {
            super('block');
        }
    }

    return BlockLink;
});
