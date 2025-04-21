<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Block\Cart\MeasurementElements;

interface LayoutProcessorInterface
{
    /**
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process(array $jsLayout): array;
}