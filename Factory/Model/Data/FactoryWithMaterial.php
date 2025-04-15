<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\Factory\Model\Data;

use BelVG\Factory\Api\Data\FactoryWithMaterialInterface;
use Magento\Framework\DataObject;

class FactoryWithMaterial extends DataObject implements FactoryWithMaterialInterface
{
    /**
     * @inheritDoc
     */
    public function getFactoryId(): ?int
    {
        return $this->getData(self::FACTORY_ID) === null ? null
            : (int)$this->getData(self::FACTORY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setFactoryId(?int $factoryId): void
    {
        $this->setData(self::FACTORY_ID, $factoryId);
    }

    /**
     * @inheritDoc
     */
    public function getMaterialId(): ?int
    {
        return $this->getData(self::MATERIAL_ID) === null ? null
            : (int)$this->getData(self::MATERIAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMaterialId(?int $materialId): void
    {
        $this->setData(self::MATERIAL_ID, $materialId);
    }

    /**
     * @inheritDoc
     */
    public function getMaterialIdentifier(): ?string
    {
        return $this->getData(self::MATERIAL_IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setMaterialIdentifier(?string $materialIdentifier): void
    {
        $this->setData(self::MATERIAL_IDENTIFIER, $materialIdentifier);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID) === null ? null
            : (int)$this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId(?int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): ?int
    {
        return $this->getData(self::PRIORITY) === null ? null
            : (int)$this->getData(self::PRIORITY);
    }

    /**
     * @inheritDoc
     */
    public function setPriority(?int $priority): void
    {
        $this->setData(self::PRIORITY, $priority);
    }
}
