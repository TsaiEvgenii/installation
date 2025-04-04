<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model\Service\RetrieveAdditionalPricesFromCustomOptions;

interface RetrieveAdditionalPricesFromCustomOptionsProcessorInterface
{
    /**
     * @param array $customOptions
     *
     * @return \BelVG\InstallationElements\Api\Data\AdditionalPriceInterface[]
     */
    public function process(array $customOptions): array;
}