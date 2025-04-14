<?php


namespace BelVG\LayoutCustomizer\Api\Data;

interface LayoutSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Layout list.
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface[]
     */
    public function getItems();

    /**
     * Set identifier list.
     * @param \BelVG\LayoutCustomizer\Api\Data\LayoutInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
