<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Api\Webapi;

interface CustomerElementsManagerInterface
{
    /**
     * @param int $customerId
     * @return \BelVG\MeasurementTool\Api\Data\CustomerElementInterface[]
     */
    public function getCustomerElements(int $customerId): array;

    /**
     * @param int $customerId
     * @param int $elementId
     *
     * @return bool
     */
    public function removeCustomerElement(int $customerId, int $elementId):bool;
}