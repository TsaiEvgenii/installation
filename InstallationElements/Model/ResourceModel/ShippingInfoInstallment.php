<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShippingInfoInstallment extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_shippingmanager_shippinginfo_installment_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('belvg_shippingmanager_shippinginfo_installment', 'installment_id');
        $this->_useIsObjectNew = true;
    }
}
