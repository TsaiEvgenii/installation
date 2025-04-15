<?php
namespace BelVG\Factory\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;

class DeliveryRule extends AbstractDb
{
    protected $colorsTable;

    protected function _construct()
    {
        $this->_init('belvg_factory_material_delivery', 'delivery_rule_id');
        $this->colorsTable = $this->getTable('belvg_factory_material_delivery_colors');
    }
}
