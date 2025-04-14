define([
    './height',
    './width'
], function(Height, Width) {
    let list = {};
    for (let i = 0; i < arguments.length; ++i) {
        let item = arguments[i];
        list[item.Type] = item;
    }
    return list;
});
