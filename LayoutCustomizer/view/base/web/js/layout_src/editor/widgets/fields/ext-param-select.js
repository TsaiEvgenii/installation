define([
    './_ext-value-select'
], function(ExtValueSelect) {

    class ExtParamSelect extends ExtValueSelect.Widget {
        constructor(context, params) {
            super(context, params, context.config.ExtParams || []);
        }
    }

    return {Widget: ExtParamSelect};
});
