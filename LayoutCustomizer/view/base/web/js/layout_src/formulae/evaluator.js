define(function() {

    let ObjectPropertyRegex = /\$\{(\.+)}\.([A-Za-z0-9\._]+)/;

    class Evaluator {
        constructor(context) {
            this._context = context; 
        }

        evaluate(expr, params) {
            return (typeof expr === 'function')
                ? this._evalFunction(expr, params)
                : this._evalString(expr, params);
        }

        // NOTE: eval disabled for now, converting to number instead
        _evalString(expr, params) {
            // let om = this._context.objectManager;
            // TODO: replace references, e.g. ${block:1}.width -> 100
            // return eval(expr);
            let value = Number(expr);
            return !isNaN(value)
                ? value
                : null;
        }

        _evalFunction(f, params) {
            return f.call(null, this._context, params);
        }
    }

    return {Evaluator: Evaluator};
});
