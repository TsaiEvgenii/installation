<?php


namespace BelVG\LayoutCustomizer\Model\Report;

use Magento\Catalog\Model\Product\Type as ProductType;
use Traversable;

class SimpleProducts implements \IteratorAggregate
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $productCollection;
    /**
     * @var int
     */
    private $batchSize;

    /**
     * SimpleProducts constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        $batchSize = 1000
    ) {
        $this->productCollection = $productCollection;
        $this->batchSize = $batchSize;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() :Traversable
    {
        $this->productCollection->setPageSize($this->batchSize);
        $this->productCollection->addAttributeToFilter('type_id', ['eq' => ProductType::TYPE_SIMPLE]);
        //$this->productCollection->addOrder('entity_id', 'DESC');
        $this->productCollection->addAttributeToSelect('status');
        $lastPage = $this->productCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->productCollection->clear();
            $this->productCollection->setCurPage($pageNumber);
            foreach ($this->productCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }

    public function addAttributeToFilter($attribute, $value, $operator = 'eq')
    {
        $this->productCollection->addAttributeToFilter($attribute, [$operator => $value]);
    }

    public function reset()
    {
        $this->productCollection->getSelect()->reset(\Zend_Db_Select::WHERE);
    }

}
