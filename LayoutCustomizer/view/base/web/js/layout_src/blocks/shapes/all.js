define([
    './circle',
    './diamond',
    './rectangle',
    './semicircle',
    './triangle',
    './trunc-triang',
    './vert-triang'
], function() {
    let list = {};
    for (let i = 0; i < arguments.length; ++i) {
        let item = arguments[i];
        list[item.Type] = item;
    }
    return list;
});
