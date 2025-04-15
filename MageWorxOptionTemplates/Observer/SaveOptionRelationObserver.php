<?php
/*
 * @package Vinduesgrossisten.
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Observer;

use BelVG\MageWorxOptionTemplates\Model\OptionSaver;
use MageWorx\OptionBase\Model\Entity\Group as GroupEntity;
use MageWorx\OptionBase\Model\Entity\Product as ProductEntity;
use MageWorx\OptionTemplates\Model\GroupFactory as GroupFactory;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class SaveOptionRelationObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     *
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     *
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $joinProcessor;

    /**
     *
     * @var ProductEntity
     */
    protected $productEntity;

    /**
     *
     * @var GroupEntity
     */
    protected $groupEntity;

    /**
     * @var bool
     */
    protected $optionsInitialized;

    protected $options;

    protected $optionSaver;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\Registry             $registry,
        \Magento\Framework\App\RequestInterface $request,
        GroupCollectionFactory                  $groupCollectionFactory,
        GroupFactory                            $groupFactory,
        OptionSaver                             $optionSaver,
        JoinProcessorInterface                  $joinProcessor,
        ProductEntity                           $productEntity,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        GroupEntity                             $groupEntity
    )
    {
        $this->registry = $registry;
        $this->request = $request;
        $this->groupFactory = $groupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->productEntity = $productEntity;
        $this->groupEntity = $groupEntity;
        $this->optionSaver = $optionSaver;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        if ($this->_out($product)) {
            return;
        }

        $productId = $product->getId();
        $optionsInfo = $this->getOptionsInfo();
        $newGroupIds = $optionsInfo['newGroupIds'];
        $delGroupIds = $optionsInfo['delGroupIds'];

        $realNewGroups = [];
        $realDelGroupIds = $delGroupIds;

        $productOptions = $this->productEntity->getOptionsAsArray($this->resetOption($product));

        /** @var \MageWorx\OptionTemplates\Model\Group $group */
        $group = $this->groupFactory->create();

        $groupIds = array_merge($newGroupIds + $delGroupIds);
        /** @var \MageWorx\OptionTemplates\Model\ResourceModel\Group\Collection $collection */
        $collection = $this->groupCollectionFactory->create();
        $collection->addFieldToFilter('group_id', $groupIds);

        /** @var \MageWorx\OptionTemplates\Model\Group $group */
        foreach ($collection as $group) {
            $groupOptionsIds = array_keys($this->groupEntity->getOptionsAsArray($group));
            if (in_array($group->getId(), $newGroupIds)) {
                if ($this->issetAllGroupOptionsInProduct($groupOptionsIds, $productOptions)) {
                    $realNewGroups[] = $group;
                }

            } elseif (in_array($group->getId(), $delGroupIds)) {
                if ($this->issetAnyGroupOptionsInProduct($groupOptionsIds, $productOptions)) {
                    unset($realDelGroupIds[array_search($group->getId(), $delGroupIds)]);
                }
            }
        }

        if ($realDelGroupIds) {
            foreach ($realDelGroupIds as $groupId) {

                $group = $this->groupFactory->create()->load($groupId);
                $group->setAffectedProductIds([$productId]);
                $group->setDelProductIds([$productId]);
                $this->optionSaver->saveProductOptions(
                    $group,
                    $group->getOptionArray(),
                    OptionSaver::SAVE_MODE_UPDATE,
                    $this->storeManager->getStore()->getId()
                );
            }
        }

        if ($realNewGroups) {
            foreach ($realNewGroups as $group) {
                $group->setAffectedProductIds([$productId]);
                $group->setUpdProductIds([$productId]);
                $this->optionSaver->saveProductOptions(
                    $group,
                    $group->getOptionArray(),
                    OptionSaver::SAVE_MODE_UPDATE,
                    $this->storeManager->getStore()->getId()
                );
            }
        }

    }

    /**
     * Reset product options - we find deleted options in product after save.
     *
     * @param $product
     * @return
     */
    protected function resetOption($product)
    {
        $product->setOptions(null);
        $collection = $product->getProductOptionsCollection();
        $this->joinProcessor->process($collection);
        foreach ($collection as $option) {
            $option->setProduct($product);
            $product->addOption($option);
        }

        return $product;
    }

    public function getOptions()
    {
        if (empty($this->options) && $this->getHasOptions() && !$this->optionsInitialized) {
            $collection = $this->getProductOptionsCollection();
            $this->joinProcessor->process($collection);
            foreach ($collection as $option) {
                $option->setProduct($this);
                $this->addOption($option);
            }
            $this->optionsInitialized = true;
        }

        return $this->options;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface[] $options
     * @return \MageWorx\OptionTemplates\Model\Observer\SaveOptionRelationObserver
     */
    public function setOptions(array $options = null)
    {
        $this->options = $options;
        if (is_array($options) && empty($options)) {
            $this->setData('is_delete_options', true);
        }
        $this->optionsInitialized = true;

        return $this;
    }

    /**
     * Check if go out
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    protected function _out($product)
    {
        if (!in_array($this->request->getFullActionName(), $this->getAvailableActions())) {
            return true;
        }

        if (!$product) {
            return true;
        }

        if (!$product->getId()) {
            return true;
        }

        $optionInfo = $this->getOptionsInfo();
        if ($optionInfo['productId'] && $optionInfo['productId'] != $product->getId()) {
            return true;
        }

        if (!$optionInfo['newGroupIds'] && !$optionInfo['delGroupIds']) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve list of available actions
     *
     * @return array
     */
    protected function getAvailableActions()
    {
        return ['catalog_product_save'];
    }

    protected function getOptionsInfo()
    {
        return $this->registry->registry('mageworx_optiontemplates_relation_data');
    }

    /**
     *
     * @param array $groupOptionsIds
     * @param array $productOptions
     * @return bool
     */
    protected function issetAllGroupOptionsInProduct($groupOptionsIds, $productOptions)
    {
        return $this->issetGroupOptionsInProduct($groupOptionsIds, $productOptions, true);
    }

    /**
     *
     * @param array $groupOptionsIds
     * @param array $productOptions
     * @return bool
     */
    protected function issetAnyGroupOptionsInProduct($groupOptionsIds, $productOptions)
    {
        return $this->issetGroupOptionsInProduct($groupOptionsIds, $productOptions);
    }

    /**
     *
     * @param array $groupOptionsIds
     * @param array $productOptions
     * @param bool $checkAll
     * @return bool
     */
    protected function issetGroupOptionsInProduct($groupOptionsIds, $productOptions, $checkAll = false)
    {
        $groupOptionsIds = array_map('strval', $groupOptionsIds);

        $productGroupOptionIds = [];
        foreach ($productOptions as $options) {
            if (!empty($options['group_option_id'])) {
                $productGroupOptionIds[] = $options['group_option_id'];
            }
        }
        $intersectOptions = array_intersect($groupOptionsIds, $productGroupOptionIds);

        if ($checkAll) {
            return $groupOptionsIds == $intersectOptions;
        }

        return ($intersectOptions) ? true : false;
    }
}
