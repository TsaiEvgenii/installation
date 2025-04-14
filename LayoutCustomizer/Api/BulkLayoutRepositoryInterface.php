<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Api;

interface BulkLayoutRepositoryInterface
{
    /**
     * Retrieve Layout matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param string $storeId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        string $storeId
    );

    /**
     * Retrieve Layout were updated since particular datetime
     *
     * @param string $datetime
     * @param int $storeId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUpdatedList(
        string $datetime,
        string $storeId
    );

    /**
     * Update layouts with $data and return IDs of the updated entities
     *
     * @param string $data
     * @param string $storeId
     * @return bool
     */
    public function saveList(
        string $data,
        string $storeId
    );
}
