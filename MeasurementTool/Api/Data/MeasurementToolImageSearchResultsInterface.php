<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface MeasurementToolImageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface[]
     */
    public function getItems();

    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}