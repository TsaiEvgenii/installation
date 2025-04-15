<?php
namespace BelVG\Factory\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface FactoryMaterialSearchResultsInterface extends SearchResultsInterface
{
    /** 
     * @return \BelVG\Factory\Api\Data\FactoryMaterialInterface[] 
     */
    public function getItems();

    /**
     * @param \BelVG\Factory\Api\Data\FactoryMaterialInterface[]
     * @return $this
     */
    public function setItems(array $items);
}
