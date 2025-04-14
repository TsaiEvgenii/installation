<?php


namespace BelVG\LayoutCustomizer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LayoutStoreRepositoryInterface
{

    /**
     * Save LayoutStore
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
    );

    /**
     * Retrieve LayoutStore
     * @param string $layoutstoreId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($layoutstoreId);

    /**
     * Retrieve LayoutStore matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete LayoutStore
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface $layoutStore
    );

    /**
     * Delete LayoutStore by ID
     * @param string $layoutstoreId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($layoutstoreId);
}