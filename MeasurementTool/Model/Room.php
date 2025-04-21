<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model;

use BelVG\MeasurementTool\Api\Data\RoomInterface;
use BelVG\MeasurementTool\Model\ResourceModel\Room as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use BelVG\MeasurementTool\Api\Data\RoomInterfaceFactory;

class Room extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_room_model';

    public function __construct(
        protected DataObjectHelper $dataObjectHelper,
        protected RoomInterfaceFactory $roomDataFactory,
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

    public function getDataModel(): RoomInterface
    {
        $roomData = $this->getData();
        /** @var RoomInterface $roomDataModel */
        $roomDataModel = $this->roomDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $roomDataModel,
            $roomData,
            RoomInterface::class
        );

        return $roomDataModel;
    }
}
