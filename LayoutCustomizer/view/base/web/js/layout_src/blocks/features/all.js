define([
    './bevel',
    './crossbars',
    './door-frame',
    './glass',
    './open-type',
    './panel',
    './primary-door',
    './radial-bars',
    './door-shape',
    './fire-escape',
    './clamp',
    './door-handle',
    './sliding-door',
    './measurement-dependency',
    './half-door'
], function() {
    let list = {};
    for (let i = 0; i < arguments.length; ++i) {
        let item = arguments[i];
        list[item.Type] = item;
    }
    return list;
})
