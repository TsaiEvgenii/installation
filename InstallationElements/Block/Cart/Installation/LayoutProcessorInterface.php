<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Block\Cart\Installation;

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