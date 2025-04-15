<?php
namespace BelVG\Factory\Model\Data;

use BelVG\Factory\Api\Data\DeliveryRule;

use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class FactoryMaterial
    extends AbstractExtensibleObject
    implements FactoryMaterialInterface
{
    public function getFactoryMaterialId()
    {
        return $this->_get(self::FACTORY_MATERIAL_ID);
    }

    public function setFactoryMaterialId($factoryMaterialId)
    {
        return $this->setData(self::FACTORY_MATERIAL_ID, $factoryMaterialId);
    }

    public function getFactoryId()
    {
        return $this->_get(self::FACTORY_ID);
    }

    public function setFactoryId($factoryId)
    {
        return $this->setData(self::FACTORY_ID, $factoryId);
    }

    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function getPriority()
    {
        return $this->_get(self::PRIORITY);
    }

    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    public function getMaterialId()
    {
        return $this->_get(self::MATERIAL_ID);
    }

    public function setMaterialId($materialId)
    {
        return $this->setData(self::MATERIAL_ID, $materialId);
    }

    public function getDeliveryRules()
    {
        return $this->_get(self::DELIVERY_ITEMS);
    }

    public function setDeliveryRules(array $items)
    {
        return $this->setData(self::DELIVERY_ITEMS, $items);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\FactoryMaterialExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
