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
    './clamp'
], function(Bevel, Crossbars, DoorFrame, Glass, OpenType, Panel, PrimaryDoor, RadialBars, DoorShape, FireEscape, Clamp) {
    return {
        bevel: Bevel,
        crossbars: Crossbars,
        "door-frame": DoorFrame,
        glass: Glass,
        "open-type": OpenType,
        panel: Panel,
        "primary-door": PrimaryDoor,
        "radial-bars": RadialBars,
        'door-shape': DoorShape,
        'fire-escape': FireEscape,
        'clamp': Clamp
    };
});
