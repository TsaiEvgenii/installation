<?php
namespace BelVG\Factory\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface FactorySearchResultsInterface extends SearchResultsInterface
{
    /** 
     * @return \BelVG\Factory\Api\Data\FactoryInterface[] 
     */
    public function getItems();

    /**
     * @param \BelVG\Factory\Api\Data\FactoryInterface[]
     * @return $this
     */
    public function setItems(array $items);
}
