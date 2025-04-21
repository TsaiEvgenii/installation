<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api;

interface RoomRepositoryInterface
{
    /**
     * @param \BelVG\MeasurementTool\Api\Data\RoomInterface $room
     * @return \BelVG\MeasurementTool\Api\Data\RoomInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\MeasurementTool\Api\Data\RoomInterface $room
    ): \BelVG\MeasurementTool\Api\Data\RoomInterface;

    /**
     * @param int $roomId
     * @return \BelVG\MeasurementTool\Api\Data\RoomInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $roomId): \BelVG\MeasurementTool\Api\Data\RoomInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\MeasurementTool\Api\Data\RoomSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \BelVG\MeasurementTool\Api\Data\RoomInterface $room
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\MeasurementTool\Api\Data\RoomInterface $room
    ): bool;

    /**
     * @param int $roomId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $roomId): bool;
}