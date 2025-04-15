<?php
namespace BelVG\Factory\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface DeliveryRuleInterface
    extends ExtensibleDataInterface
{
    const DELIVERY_RULE_ID    = 'delivery_rule_id';
    const FACTORY_MATERIAL_ID = 'factory_material_id';
    const STORE_ID            = 'store_id';
    const CATEGORY_ID         = 'category_id';
    const COLORS              = 'colors';
    const DELIVERY_TIME       = 'delivery_time';
    const SORT_ORDER          = 'sort_order';
    public const TYPES = 'types';

    /**
     * @return int
     */
    public function getDeliveryRuleId();

    /**
     * @param int $deliveryRuleId
     * @return $this
     */
    public function setDeliveryRuleId($deliveryRuleId);

    /**
     * @return int
     */
    public function getFactoryMaterialId();

    /**
     * @param int $factoryMaterialId
     * @return $this
     */
    public function setFactoryMaterialId($factoryMaterialId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);


    /**
     * @return int
     */
    public function getScore();

    /**
     * @return int|null
     */
    public function getCategoryId();

    /**
     * @param int|null $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * @return string
     */
    public function getColors();


    /**
     * @param string|null $colors
     * @return $this
     */
    public function setColors($colors);

    /**
     * @return int
     */
    public function getDeliveryTime();

    /**
     * @param int $deliveryTime
     * @return $this
     */
    public function setDeliveryTime($deliveryTime);

    /**
     * @return int
     */
    public function getDeliveryDays();

    /**
     * @return int
     */
    public function getDeliveryWeeks();

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int
     * @return $this
     */
    public function setSortOrder($position);

    /**
     * @return string
     */
    public function getTypes(): string;

    /**
     * @param string $types
     * @return $this
     */
    public function setTypes(string $types): DeliveryRuleInterface;

    /**
     * @return \BelVG\Factory\Api\Data\DeliveryRuleExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \BelVG\Factory\Api\Data\DeliveryRuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\DeliveryRuleExtensionInterface $extensionAttributes);
}
