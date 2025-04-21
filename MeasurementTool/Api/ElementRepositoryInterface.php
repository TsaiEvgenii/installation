<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */

namespace BelVG\MeasurementTool\Api;

interface ElementRepositoryInterface
{
    /**
     * @param \BelVG\MeasurementTool\Api\Data\ElementInterface $element
     * @return \BelVG\MeasurementTool\Api\Data\ElementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\MeasurementTool\Api\Data\ElementInterface $element
    );

    /**
     * @param int $elementId
     * @return \BelVG\MeasurementTool\Api\Data\ElementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $elementId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\MeasurementTool\Api\Data\ElementSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \BelVG\MeasurementTool\Api\Data\ElementInterface $element
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\MeasurementTool\Api\Data\ElementInterface $element
    );

    /**
     * @param int $elementId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($elementId);
}