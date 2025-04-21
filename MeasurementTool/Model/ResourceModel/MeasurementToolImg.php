<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MeasurementToolImg extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_measurement_tool_img_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('belvg_measurement_tool_img', 'entity_id');
        $this->_useIsObjectNew = true;
    }
}
