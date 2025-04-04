<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model;

use BelVG\InstallationElements\Model\ResourceModel\ShippingInfoInstallment as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class ShippingInfoInstallment extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_shippingmanager_shippinginfo_installment_model';

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
