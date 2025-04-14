/*
 * @package Vinduesgrossisten.
 * @author Anastasiya Misiukevich <nastya.misiukevich@gmail.com>
 * Copyright (c) 2025.
 */

define([
    '../color',
    '../feature',
    '../geometry'
], function(Color, Feature, Geom) {

    var Type = 'half-door',
        Name = 'Half Door';

    class HalfDoor extends Feature.Base {
        constructor() {
            super(Type);
        }
    }

    return {
        Type: Type,
        Name: Name,
        Feature: HalfDoor
    };
});
