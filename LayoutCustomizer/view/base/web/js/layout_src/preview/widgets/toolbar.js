define([
    '../../ui/widgets/toolbar',
    './toolbar/all'
], function(Base, ToolList) {

    class Toolbar extends Base.Base {
        constructor(context) {
            super(context, ToolList);
        }
    }

    return {Widget: Toolbar};
});
