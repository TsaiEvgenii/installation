<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model;

use BelVG\InstallationElements\Model\ResourceModel\InstallationOrderTicket as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class InstallationOrderTicket extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_installation_order_ticket_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
