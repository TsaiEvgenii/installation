define(function() {
    class Format {
        constructor(drawer) {
            this._drawer = drawer;
            this._params = {};
        }

        setParams() {
            let precision = this._drawer.numberPrecision;
            this._params = {style: 'decimal'};
            if (precision !== null) {
                this._params.maximumFractionDigits = precision;
            }
        }

        decimal(value) {
            this.setParams();
            let formatter = new Intl.NumberFormat(this._drawer.locale, this._params);
            return formatter.format(value);
        }

        toDecimal (value) {
            this.setParams();
            return parseFloat(value).toFixed(this._params.maximumFractionDigits);
        }
    }

    return Format;
});
