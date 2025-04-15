<?php
namespace BelVG\Factory\Model\Validator;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Tree as TreeResource;
use Magento\Store\Api\Data\StoreInterface;

class CategorySource
{
    protected $collectionFactory;
    protected $treeResource;

    public function __construct(
        CollectionFactory $collectionFactory,
        TreeResource $treeResource)
    {
        $this->collectionFactory = $collectionFactory;
        $this->treeResource = $treeResource;
    }

    public function getStoreCategories(StoreInterface $store)
    {
        $list = [];
        $addNodeToList = function($node) use (&$addNodeToList, &$list) {
            // Add category
            if ($node->getProductCount() > 0)
                $list[$node->getId()] = $node->getName();

            // Add children
            foreach ($node->getChildren() as $child)
                $addNodeToList($child);
        };
        $addNodeToList($this->getRootNode($store));
        return $list;
    }

    protected function getRootNode(StoreInterface $store)
    {
        $rootCategoryId = $store->getRootCategoryId();
        $tree = $this->treeResource->load(null);
        $collection = $this->createCollection($store);
        $tree->addCollectionData($collection);
        $rootCategory = $tree->getNodeById($rootCategoryId);
        return $rootCategory;
    }

    protected function createCollection(StoreInterface $store)
    {
        return $this->collectionFactory
            ->create()
            ->addAttributeToSelect('name')
            ->setLoadProductCount(true)
            ->setStoreId($store->getId());
    }
}
