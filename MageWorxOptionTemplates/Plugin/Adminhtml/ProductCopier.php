<?php

/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Plugin\Adminhtml;

use Magento\Store\Model\Store;
use BelVG\MageWorxOptionTemplates\Model\OptionSaver;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\CollectionFactory as TemplateGroupCollectionFactory;

/**
 * Class ProductCopier
 * @package BelVG\MageWorxOptionTemplates\Plugin\Adminhtml
 */
class ProductCopier
{

    /**
     * @var OptionSaver
     */
    protected $optionSaver;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TemplateGroupCollectionFactory
     */
    protected $templateGroupCollectionFactory;

    /**
     * ProductCopier constructor.
     * @param OptionSaver $optionSaver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param TemplateGroupCollectionFactory $templateGroupCollectionFactory
     */
    public function __construct(
        OptionSaver $optionSaver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TemplateGroupCollectionFactory $templateGroupCollectionFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->templateGroupCollectionFactory = $templateGroupCollectionFactory;
        $this->optionSaver = $optionSaver;
    }

    /**
     * @param $subject
     * @param $result
     * @param $arguments
     * @return mixed
     */
    public function afterCopy($subject, $result, $arguments)
    {
        $optionGroupCollection = $this->templateGroupCollectionFactory->create();
        $templateIds = [];
        foreach ($arguments->getOptions() as $option) {
            $templateIds[] = $option->getGroupId();
        }
        if ($templateIds) {
            $optionGroupCollection->addFieldToFilter('group_id', ['in' => $templateIds]);
            foreach ($this->storeManager->getStores() as $store) {
                $this->storeManager->setCurrentStore($store->getId());
                foreach ($optionGroupCollection as $optionGroup) {
                    $optionGroup->setUpdProductIds([$result->getEntityId()]);
                    $optionGroup->setAffectedProductIds([$result->getEntityId()]);
                    $this->optionSaver->saveProductOptions(
                        $optionGroup,
                        $optionGroup->getOptionArray(),
                        OptionSaver::SAVE_MODE_UPDATE,
                        $store->getId()
                    );
                }
            }
        }
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);

        return $result;
    }

}
