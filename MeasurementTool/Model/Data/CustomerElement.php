<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model\Data;

use BelVG\MeasurementTool\Api\Data\CustomerElementInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CustomerElement extends AbstractExtensibleModel  implements CustomerElementInterface
{
    /**
     * Getter for EntityId.
     *
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID) === null ? null
            : (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * Getter for CustomerId.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID) === null ? null
            : (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Setter for CustomerId.
     *
     * @param int|null $customerId
     *
     * @return void
     */
    public function setCustomerId(?int $customerId): void
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Getter for ElementId.
     *
     * @return int|null
     */
    public function getElementId(): ?int
    {
        return $this->getData(self::ELEMENT_ID) === null ? null
            : (int)$this->getData(self::ELEMENT_ID);
    }

    /**
     * Setter for ElementId.
     *
     * @param int|null $elementId
     *
     * @return void
     */
    public function setElementId(?int $elementId): void
    {
        $this->setData(self::ELEMENT_ID, $elementId);
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
     * Getter for RoomId.
     *
     * @return int|null
     */
    public function getRoomId(): ?int
    {
        return $this->getData(self::ROOM_ID) === null ? null
            : (int)$this->getData(self::ROOM_ID);
    }

    /**
     * Setter for RoomId.
     *
     * @param int|null $roomId
     *
     * @return void
     */
    public function setRoomId(?int $roomId): void
    {
        $this->setData(self::ROOM_ID, $roomId);
    }

    /**
     * Getter for RoomName.
     *
     * @return string|null
     */
    public function getRoomName(): ?string
    {
        return $this->getData(self::ROOM_NAME);
    }

    /**
     * Setter for RoomName.
     *
     * @param string|null $roomName
     *
     * @return void
     */
    public function setRoomName(?string $roomName): void
    {
        $this->setData(self::ROOM_NAME, $roomName);
    }

    /**
     * Getter for Type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Setter for Type.
     *
     * @param string|null $type
     *
     * @return void
     */
    public function setType(?string $type): void
    {
        $this->setData(self::TYPE, $type);
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
     * Getter for Width.
     *
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->getData(self::WIDTH) === null ? null
            : (float)$this->getData(self::WIDTH);
    }

    /**
     * Setter for Width.
     *
     * @param float|null $width
     *
     * @return void
     */
    public function setWidth(?float $width): void
    {
        $this->setData(self::WIDTH, $width);
    }

    /**
     * Getter for Height.
     *
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->getData(self::HEIGHT) === null ? null
            : (float)$this->getData(self::HEIGHT);
    }

    /**
     * Setter for Height.
     *
     * @param float|null $height
     *
     * @return void
     */
    public function setHeight(?float $height): void
    {
        $this->setData(self::HEIGHT, $height);
    }

    /**
     * Getter for Qty.
     *
     * @return int|null
     */
    public function getQty(): ?int
    {
        return $this->getData(self::QTY) === null ? null
            : (int)$this->getData(self::QTY);
    }

    /**
     * Setter for Qty.
     *
     * @param int|null $qty
     *
     * @return void
     */
    public function setQty(?int $qty): void
    {
        $this->setData(self::QTY, $qty);
    }
    /**
     * @return \BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): static
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
