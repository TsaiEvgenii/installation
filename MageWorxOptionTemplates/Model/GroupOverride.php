<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

/**
 * override BelVG\MageWorxOptionsSaveFix\Override\Model\GroupOverride
 */

namespace BelVG\MageWorxOptionTemplates\Model;

use BelVG\MageWorxOptionsSaveFix\Override\Model\GroupOverride as PreviousOverride;
use Magento\Catalog\Model\Product\OptionFactory as ProductOptionFactory;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Model\Entity\Group as GroupEntity;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionTemplates\Model\Group\OptionFactory as GroupOptionFactory;


class GroupOverride extends PreviousOverride
{

    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        GroupOptionFactory $groupOptionFactory,
        ProductOptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $productOptionCollectionFactory,
        \Magento\Catalog\Model\Product\Configuration\Item\OptionFactory $itemOptionFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $joinProcessor,
        \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry,
        GroupEntity $groupEntity, BaseHelper $baseHelper, CollectionUpdaterRegistry $collectionUpdaterRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct(
            $productFactory,
            $productOptionConfig,
            $groupOptionFactory,
            $productOptionFactory,
            $productOptionCollectionFactory,
            $itemOptionFactory,
            $joinProcessor,
            $context,
            $registry,
            $groupEntity,
            $baseHelper,
            $collectionUpdaterRegistry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Convert Group to Product entity for using option
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function convertGroupToProduct()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $product->setData($this->getData())
            ->setId($this->getId())
            ->setStoreId($this->storeManager->getStore()->getId());

        return $product;
    }
}
