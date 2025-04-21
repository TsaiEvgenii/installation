<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model;

use BelVG\MeasurementTool\Model\ResourceModel\MeasurementToolImg as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterfaceFactory;

class MeasurementToolImg extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_img_model';

    public function __construct(
        protected DataObjectHelper $dataObjectHelper,
        protected MeasurementToolImageInterfaceFactory $measurementToolImgDataFactory,
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
    public function getDataModel(): \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface
    {
        $measurementToolImgData = $this->getData();
        /** @var MeasurementToolImageInterface $measurementToolImgDataModel */
        $measurementToolImgDataModel = $this->measurementToolImgDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $measurementToolImgDataModel,
            $measurementToolImgData,
            MeasurementToolImageInterface::class
        );

        return $measurementToolImgDataModel;
    }
}
