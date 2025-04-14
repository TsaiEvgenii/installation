define([
    './_ext-value-select'
], function(ExtValueSelect) {

    class ExtOptionSelect extends ExtValueSelect.Widget {
        constructor(context, params) {
            super(context, params, context.config.ExtOptions || []);
        }
    }

    return {Widget: ExtOptionSelect};
});
