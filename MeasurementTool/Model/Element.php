<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model;

use BelVG\MeasurementTool\Model\ResourceModel\Element as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use BelVG\MeasurementTool\Api\Data\ElementInterface;
use BelVG\MeasurementTool\Api\Data\ElementInterfaceFactory;


class Element extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_element_model';

    public function __construct(
        protected DataObjectHelper $dataObjectHelper,
        protected ElementInterfaceFactory $elementDataFactory,
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

    public function getDataModel(): ElementInterface
    {
        $elementData = $this->getData();
        /** @var ElementInterface $elementDataModel */
        $elementDataModel = $this->elementDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $elementDataModel,
            $elementData,
            ElementInterface::class
        );

        return $elementDataModel;
    }
}
