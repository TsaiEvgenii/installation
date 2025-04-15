<?php
namespace BelVG\Factory\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface FactoryMaterialInterface extends ExtensibleDataInterface
{
    const FACTORY_MATERIAL_ID = 'factory_material_id';
    const FACTORY_ID          = 'factory_id';
    const MATERIAL_ID         = 'material_id';
    const STORE_ID            = 'store_id';
    const PRIORITY            = 'priority';
    const DELIVERY_ITEMS      = 'delivery_rules';

    /**
     * @return int
     */
    public function getFactoryMaterialId();

    /**
     * @param int
     * @return $this
     */
    public function setFactoryMaterialId($factoryMaterialId);

    /**
     * @return int
     */
    public function getFactoryId();

    /**
     * @param int
     * @return $this
     */
    public function setFactoryId($factoryId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int
     * @return $this
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getMaterialId();

    /**
     * @param int
     * @return $this
     */
    public function setMaterialId($materialId);

    /**
     * @return \BelVG\Factory\Api\Data\DeliveryRuleInterface[]
     */
    public function getDeliveryRules();

    /**
     * @params \BelVG\Factory\Api\Data\DeliveryRuleInterface[] $items
     * @return $this
     */
    public function setDeliveryRules(array $items);

    /**
     * @return \BelVG\Factory\Api\Data\FactoryMaterialExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \BelVG\Factory\Api\Data\FactoryMaterialExtensionInterface
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\FactoryMaterialExtensionInterface $extensionAttributes);
}
