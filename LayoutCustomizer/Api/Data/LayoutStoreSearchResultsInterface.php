<?php


namespace BelVG\LayoutCustomizer\Api\Data;

interface LayoutStoreSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get LayoutStore list.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface[]
     */
    public function getItems();

    /**
     * Set layout_id list.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}