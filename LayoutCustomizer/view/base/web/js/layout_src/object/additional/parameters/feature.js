define([
    '../parameter'
], function(Parameter) {

    class FeatureParameter extends Parameter.Base {
        constructor() {
            super('feature', 'params.');
        }
    }

    return FeatureParameter;
});
