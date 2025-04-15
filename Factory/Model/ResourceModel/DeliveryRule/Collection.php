<?php
namespace BelVG\Factory\Model\ResourceModel\DeliveryRule;

use BelVG\Factory\Model\DeliveryRule;
use BelVG\Factory\Model\ResourceModel\DeliveryRule as DeliveryRuleResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(DeliveryRule::class, DeliveryRuleResource::class);
    }

    /**
     * @param int|array $factoryMaterialId
     * @return $this
     */
    public function addFactoryMaterialFilter($factoryMaterialId)
    {
        return $this->addFieldToFilter('factory_material_id', $factoryMaterialId);
    }

    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', $storeId);
    }
}
