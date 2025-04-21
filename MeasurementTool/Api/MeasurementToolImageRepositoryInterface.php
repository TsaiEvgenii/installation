<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api;

interface MeasurementToolImageRepositoryInterface
{
    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface $img
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface $img
    ): \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;

    /**
     * @param int $imgId
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $imgId): \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolImageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface $img
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface $img
    ): bool;

    /**
     * @param int $imgId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $imgId): bool;
}