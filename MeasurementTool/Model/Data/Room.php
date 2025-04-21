<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model\Data;

use BelVG\MeasurementTool\Api\Data\RoomInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Room  extends AbstractExtensibleModel implements RoomInterface
{
    /**
     * Getter for RoomId.
     *
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID) === null ? null
            : (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * Getter for MeasurementToolId.
     *
     * @return int|null
     */
    public function getMeasurementToolId(): ?int
    {
        return $this->getData(self::MEASUREMENT_TOOL_ID) === null ? null
            : (int)$this->getData(self::MEASUREMENT_TOOL_ID);
    }

    /**
     * Setter for MeasurementToolId.
     *
     * @param int|null $measurementToolId
     *
     * @return void
     */
    public function setMeasurementToolId(?int $measurementToolId): void
    {
        $this->setData(self::MEASUREMENT_TOOL_ID, $measurementToolId);
    }


    /**
     * @return int|null
     */
    public function getRecordId(): ?int
    {
        return $this->getData(self::RECORD_ID) === null ? null
            : (int)$this->getData(self::RECORD_ID);
    }

    /**
     * @param int|null $measurementToolId
     *
     * @return void
     */
    public function setRecordId(?int $measurementToolId): void
    {
        $this->setData(self::RECORD_ID, $measurementToolId);
    }

    /**
     * Getter for Name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * Setter for Name.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * Getter for Elements.
     *
     * @return \BelVG\MeasurementTool\Api\Data\ElementInterface[]|null
     */
    public function getElements(): ?array
    {
        return $this->getData(self::ELEMENTS) === null ? null
            : $this->getData(self::ELEMENTS);
    }

    /**
     * Setter for Elements.
     *
     * @param \BelVG\MeasurementTool\Api\Data\ElementInterface[]|null $elements
     *
     * @return void
     */
    public function setElements(?array $elements): void
    {
        $this->setData(self::ELEMENTS, $elements);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT) === null ? null
            : $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     *
     * @param string|null $createdAt
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return \BelVG\MeasurementTool\Api\Data\RoomExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\BelVG\MeasurementTool\Api\Data\RoomExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\RoomExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): static
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
