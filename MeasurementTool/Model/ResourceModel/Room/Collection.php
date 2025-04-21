<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model\ResourceModel\Room;

use BelVG\MeasurementTool\Model\ResourceModel\Room as ResourceModel;
use BelVG\MeasurementTool\Model\Room as Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_room_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
