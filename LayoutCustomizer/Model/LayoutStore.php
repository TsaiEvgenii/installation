<?php


namespace BelVG\LayoutCustomizer\Model;

use BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterfaceFactory;
use BelVG\LayoutCustomizer\Api\Data\LayoutStoreInterface;
use Magento\Framework\Api\DataObjectHelper;

class LayoutStore extends \Magento\Framework\Model\AbstractModel
{

    protected $layoutstoreDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'belvg_layoutcustomizer_layoutstore';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param LayoutStoreInterfaceFactory $layoutstoreDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore $resource
     * @param \BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        LayoutStoreInterfaceFactory $layoutstoreDataFactory,
        DataObjectHelper $dataObjectHelper,
        \BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore $resource,
        \BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore\Collection $resourceCollection,
        array $data = []
    ) {
        $this->layoutstoreDataFactory = $layoutstoreDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve layoutstore model with layoutstore data
     * @return LayoutStoreInterface
     */
    public function getDataModel()
    {
        $layoutstoreData = $this->getData();

        $layoutstoreDataObject = $this->layoutstoreDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $layoutstoreDataObject,
            $layoutstoreData,
            LayoutStoreInterface::class
        );

        return $layoutstoreDataObject;
    }
}