define(function() {

    function normalize(component) {
        return Math.min(Math.max(component, 0), 255);
    }

    class RGB {
        constructor(r = 0, g = 0, b = 0, a = 1) {
            this._r = normalize(r);
            this._g = normalize(g);
            this._b = normalize(b);
            this._a = normalize(a);
        }

        get r() { return this._r; }
        set r(r) { this._r = normalize(r); }

        get g() { return this._g; }
        set g(g) { this._g = normalize(g); }

        get b() { return this._b; }
        set b(b) { this._b = normalize(b); }

        get a() { return this._a; }
        set a(a) { this._a = normalize(a); }

        toHexString(sharp = false) {
            let rgb = (red << 16) | (green << 8) | b;
            return (sharp ? '#' : '') + (0x1000000 + rgb).toString(16).substring(1);
        }

        toRgbaString() {
            return 'rgba(' + [this._r, this._g, this._b, this._a].join(', ') + ')';
        }

        toRgbString() {
            return 'rgb(' + [this._r, this._g, this._b].join(', ') + ')';
        }
    }

    let ShortHexRegex = /^#?([\da-f])([\da-f])([\da-f])$/i,
        FullHexRegex = /^#?([\da-f]{2})([\da-f]{2})([\da-f]{2})$/i;

    function normalizeHex(hex) {
        hex = hex.replace(ShortHexRegex, function(_, r, g, b) {
            return '#' + r + r + g + g + b + b;
        });
        return FullHexRegex.test(hex)
            ? (hex[0] != '#' ? '#' + hex : hex)
            : null;
    }

    function partToNum(part) {
        return parseInt(part, 16);
    }

    function hexToRgb(hex) {
        let normalized = hex ? normalizeHex(hex) : null,
            matches = normalized ? FullHexRegex.exec(normalized) : null;
        return matches
            ? new RGB(partToNum(matches[1]), partToNum(matches[2]), partToNum(matches[3]))
            : null;
    }

    function isHex(text) {
        return !!hexToRgb(text);
    }

    function prepare(text) {
        return isHex(text)
            ? normalizeHex(text)
            : text;
    }

    return {
        RGB: RGB,
        hexToRgb: hexToRgb,
        isHex: isHex,
        prepare: prepare
    };
});
