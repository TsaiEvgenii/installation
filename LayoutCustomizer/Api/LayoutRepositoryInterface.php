<?php


namespace BelVG\LayoutCustomizer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LayoutRepositoryInterface
{
    /**
     * Save Layout
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutInterface $layout
     * @param string $storeId
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \BelVG\LayoutCustomizer\Api\Data\LayoutInterface $layout,
        $storeId = null
    );

    /**
     * Retrieve Layout
     * @param string $layoutId
     * @param string $storeId (optional)
     * @param bool $withOptions (optional)
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($layoutId, $storeId = null, $withOptions = false);

    /**
     * Retrieve Layout
     * @param string $identifier
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByIdentifier($identifier, $storeId = null);

    /**
     * Retrieve Layout matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Layout
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutInterface $layout
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \BelVG\LayoutCustomizer\Api\Data\LayoutInterface $layout
    );

    /**
     * Delete Layout by ID
     * @param string $layoutId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($layoutId);
}
