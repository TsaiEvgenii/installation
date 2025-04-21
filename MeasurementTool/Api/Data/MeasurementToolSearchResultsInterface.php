<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface MeasurementToolSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface[]
     */
    public function getItems();

    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}