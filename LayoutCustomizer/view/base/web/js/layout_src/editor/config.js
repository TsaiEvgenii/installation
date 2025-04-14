define([
    './config/general',
    './config/form',
    './config/data',
    './config/hilight',
    './config/object'
], function(General, Form, Data, Hilight, Objekt) {
    return {
        General: General,
        Form: Form,
        Data: Data,
        Hilight: Hilight,
        "Object": Objekt,
        ExtOptions: []
    };
});
