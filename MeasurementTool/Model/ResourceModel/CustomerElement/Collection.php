<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model\ResourceModel\CustomerElement;

use BelVG\MeasurementTool\Model\CustomerElement as Model;
use BelVG\MeasurementTool\Model\ResourceModel\CustomerElement as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_customer_element_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
