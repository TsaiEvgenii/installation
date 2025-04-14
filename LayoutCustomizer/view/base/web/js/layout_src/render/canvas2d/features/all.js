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
], function(Bevel, Crossbars, DoorFrame, Glass, OpenType, Panel, PrimaryDoor, RadialBars, DoorShape, FireEscape, Clamp, DoorHandle, SlidingDoor, MeasurementDependency, HalfDoor) {
    return {
        'bevel': Bevel,
        'crossbars': Crossbars,
        'door-frame': DoorFrame,
        'glass': Glass,
        'open-type': OpenType,
        'panel': Panel,
        'primary-door': PrimaryDoor,
        'radial-bars': RadialBars,
        'door-shape': DoorShape,
        'fire-escape': FireEscape,
        'clamp': Clamp,
        'door-handle': DoorHandle,
        'sliding-door': SlidingDoor,
        'measurement-dependency': MeasurementDependency,
        'half-door': HalfDoor
    };
});
