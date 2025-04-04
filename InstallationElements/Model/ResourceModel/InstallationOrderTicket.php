<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InstallationOrderTicket extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_installation_order_ticket_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('belvg_installation_order_ticket', 'entity_id');
        $this->_useIsObjectNew = true;
    }
}
