<?php
namespace BelVG\Factory\Model\Data;

use BelVG\Factory\Api\Data\DeliveryRuleInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class DeliveryRule
    extends AbstractExtensibleObject
    implements DeliveryRuleInterface
{
    public function getDeliveryRuleId()
    {
        return $this->_get(self::DELIVERY_RULE_ID);
    }

    public function setDeliveryRuleId($deliveryRuleId)
    {
        return $this->setData(self::DELIVERY_RULE_ID, $deliveryRuleId);
    }

    public function getFactoryMaterialId()
    {
        return $this->_get(self::FACTORY_MATERIAL_ID);
    }

    public function setFactoryMaterialId($factoryMaterialId)
    {
        return $this->setData(self::FACTORY_MATERIAL_ID, $factoryMaterialId);
    }

    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function getScore()
    {
        $score = 0;
        $score = $this->getCategoryId() ? $score + 1 : $score;
        $score = $this->getColors() ? $score + 1 : $score;
        return $score;
    }

    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }

    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    public function getColors()
    {
        return $this->_get(self::COLORS);
    }

    public function setColors($colors)
    {
        return $this->setData(self::COLORS, $colors);
    }

    public function getDeliveryTime()
    {
        return $this->_get(self::DELIVERY_TIME);
    }

    public function setDeliveryTime($deliveryTime)
    {
        return $this->setData(self::DELIVERY_TIME, $deliveryTime);
    }

    public function getDeliveryDays()
    {
        $deliveryTime = $this->getDeliveryTime();
        return $deliveryTime ? $deliveryTime * 7 : $deliveryTime;
    }

    public function getDeliveryWeeks()
    {
        return $this->getDeliveryTime();
    }

    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): string
    {
        return $this->_get(self::TYPES);
    }

    /**
     * @inheritdoc
     */
    public function setTypes(string $types): DeliveryRuleInterface
    {
        return $this->setData(self::TYPES, $types);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\DeliveryRuleExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
