<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model;

use BelVG\MeasurementTool\Api\Data\CustomerElementInterface;
use BelVG\MeasurementTool\Api\Data\CustomerElementInterfaceFactory;
use BelVG\MeasurementTool\Model\ResourceModel\CustomerElement as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;

class CustomerElement extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_customer_element_model';

    public function __construct(
        protected DataObjectHelper $dataObjectHelper,
        protected CustomerElementInterfaceFactory $elementDataFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getDataModel(): CustomerElementInterface
    {
        $elementData = $this->getData();
        /** @var CustomerElementInterface $elementDataModel */
        $elementDataModel = $this->elementDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $elementDataModel,
            $elementData,
            CustomerElementInterface::class
        );

        return $elementDataModel;
    }
}
