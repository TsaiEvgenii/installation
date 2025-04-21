<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model;

use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolInterfaceFactory;
use BelVG\MeasurementTool\Model\ResourceModel\MeasurementTool as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;

class MeasurementTool extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_model';

    public function __construct(
        protected DataObjectHelper $dataObjectHelper,
        protected MeasurementToolInterfaceFactory $measurementToolDataFactory,
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

    public function getDataModel(): MeasurementToolInterface
    {
        $elementData = $this->getData();
        /** @var MeasurementToolInterface $elementDataModel */
        $elementDataModel = $this->measurementToolDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $elementDataModel,
            $elementData,
            MeasurementToolInterface::class
        );

        return $elementDataModel;
    }
}
