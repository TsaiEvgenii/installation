<?php


namespace BelVG\LayoutCustomizer\Model;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterfaceFactory;
use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\SerializerInterface;

class Layout extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'belvg_layoutcustomizer_layout';
    protected $dataObjectHelper;
    protected $storeManager;
    protected $storeId;
    protected $serializer;

    protected $layoutDataFactory;

    const STORE_ID = 'store_id';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param LayoutInterfaceFactory $layoutDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \BelVG\LayoutCustomizer\Model\ResourceModel\Layout $resource
     * @param \BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        LayoutInterfaceFactory $layoutDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \BelVG\LayoutCustomizer\Model\ResourceModel\Layout $resource,
        \BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Collection $resourceCollection,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->layoutDataFactory = $layoutDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve layout model with layout data
     * @return LayoutInterface
     */
    public function getDataModel()
    {
        $layoutData = $this->getData();

        $layoutDataObject = $this->layoutDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $layoutDataObject,
            $layoutData,
            LayoutInterface::class
        );

        return $layoutDataObject;
    }

    public function getLayoutData($layout_id, $store_id = 0)
    {
        return $this->_resource->getLayoutData($layout_id, $store_id);
    }

    public function getStoreId()
    {
        return !is_null($this->storeId)
            ? $this->storeId
            : $this->storeManager->getStore()->getId();
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    public function afterSave()
    {
        parent::afterSave();

        $blockJson = $this->getData('block_json');
        if ($blockJson) {
            $this->_saveBlockJson($blockJson);
        }

        return $this;
    }

    protected function _saveBlockJson($json)
    {
        $data = $this->serializer->unserialize($json);
        $this->_resource->saveBlockData($this, $data);
    }
}
