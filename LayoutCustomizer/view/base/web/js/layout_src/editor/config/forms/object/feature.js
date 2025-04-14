define([
    './features/bevel',
    './features/crossbars',
    './features/door-frame',
    './features/glass',
    './features/open-type',
    './features/panel',
    './features/primary-door',
    './features/radial-bars',
    './features/door-shape',
    './features/fire-escape',
    './features/clamp',
    './features/door-handle',
    './features/sliding-door',
    './features/measurement-dependency',
    './features/half-door'
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
    }
});
