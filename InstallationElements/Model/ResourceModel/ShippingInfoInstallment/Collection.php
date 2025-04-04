<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model\ResourceModel\ShippingInfoInstallment;

use BelVG\InstallationElements\Model\ResourceModel\ShippingInfoInstallment as ResourceModel;
use BelVG\InstallationElements\Model\ShippingInfoInstallment as Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'belvg_shippingmanager_shippinginfo_installment_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
