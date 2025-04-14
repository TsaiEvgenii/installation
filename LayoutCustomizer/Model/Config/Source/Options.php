<?php

namespace BelVG\LayoutCustomizer\Model\Config\Source;

use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var null
     */
    protected $collection = null;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
     */
    public $optionCollection;

    private CollectionUpdaterRegistry $collectionUpdaterRegistry;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\Collection $optionCollection,
        CollectionUpdaterRegistry $collectionUpdaterRegistry
    )
    {
        $this->request = $request;
        $this->optionCollection = $optionCollection;
        $this->collectionUpdaterRegistry = $collectionUpdaterRegistry;
    }


    public function getCollection()
    {
        if ($this->collection === NULL) {
            $storeId = (int) $this->request->getParam('store', 0);
            $options = $this->optionCollection->getOptions($storeId);
            $options->addFieldToFilter('main_table.mageworx_option_id', ['notnull' => 1]); //because there are too much old options

            $this->collection = $options;
        }

        return $this->collection;
    }

    /**
     * Options getter
     * system configuration system
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->collectionUpdaterRegistry->setCurrentEntityType('group');

        $this->getCollection();

        $result = [];
        foreach ($this->collection as $option) {
            $result[] = [
                'value' => $option->getMageworxOptionId(),
                'label' => $option->getTitle()
            ];
        }
        unset($option);

        return $result;
    }

    /**
     * Get options in "key-value" format
     * EAV system
     *
     * @return array
     */
    public function toArray()
    {
        $this->getCollection();

        $result = [];
        foreach ($this->collection as $option) {
            $result[$option->getMageworxOptionId()] = $option->getTitle();
        }
        unset($option);

        return $result;

        //return [0 => __('No'), 1 => __('Yes')];
    }
}
