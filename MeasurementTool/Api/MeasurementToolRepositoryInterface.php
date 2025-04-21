<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api;

interface MeasurementToolRepositoryInterface
{

    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface $measurementTool
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface $measurementTool
    ): \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;

    /**
     * @param int $measurementToolId
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $measurementToolId): \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface $measurementTool
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface $measurementTool
    ): bool;

    /**
     * @param int $measurementToolId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $measurementToolId): bool;
}