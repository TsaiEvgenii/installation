<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
interface CustomerElementInterface extends ExtensibleDataInterface
{
    /**
     * String constants for property names
     */
    public const ENTITY_ID = "entity_id";
    public const CUSTOMER_ID = "customer_id";
    public const ELEMENT_ID = "element_id";
    public const MEASUREMENT_TOOL_ID = "measurement_tool_id";
    public const ROOM_ID = "room_id";
    public const ROOM_NAME = "room_name";
    public const TYPE = "type";
    public const NAME = "name";
    public const WIDTH = "width";
    public const HEIGHT = "height";
    public const QTY = "qty";

    /**
     * Getter for EntityId.
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Setter for EntityId.
     *
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId(int $entityId);

    /**
     * Getter for CustomerId.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Setter for CustomerId.
     *
     * @param int|null $customerId
     *
     * @return void
     */
    public function setCustomerId(?int $customerId): void;

    /**
     * Getter for ElementId.
     *
     * @return int|null
     */
    public function getElementId(): ?int;

    /**
     * Setter for ElementId.
     *
     * @param int|null $elementId
     *
     * @return void
     */
    public function setElementId(?int $elementId): void;

    /**
     * Getter for MeasurementToolId.
     *
     * @return int|null
     */
    public function getMeasurementToolId(): ?int;

    /**
     * Setter for MeasurementToolId.
     *
     * @param int|null $measurementToolId
     *
     * @return void
     */
    public function setMeasurementToolId(?int $measurementToolId): void;

    /**
     * Getter for RoomId.
     *
     * @return int|null
     */
    public function getRoomId(): ?int;

    /**
     * Setter for RoomId.
     *
     * @param int|null $roomId
     *
     * @return void
     */
    public function setRoomId(?int $roomId): void;

    /**
     * Getter for RoomName.
     *
     * @return string|null
     */
    public function getRoomName(): ?string;

    /**
     * Setter for RoomName.
     *
     * @param string|null $roomName
     *
     * @return void
     */
    public function setRoomName(?string $roomName): void;

    /**
     * Getter for Type.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Setter for Type.
     *
     * @param string|null $type
     *
     * @return void
     */
    public function setType(?string $type): void;

    /**
     * Getter for Name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Setter for Name.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * Getter for Width.
     *
     * @return float|null
     */
    public function getWidth(): ?float;

    /**
     * Setter for Width.
     *
     * @param float|null $width
     *
     * @return void
     */
    public function setWidth(?float $width): void;

    /**
     * Getter for Height.
     *
     * @return float|null
     */
    public function getHeight(): ?float;

    /**
     * Setter for Height.
     *
     * @param float|null $height
     *
     * @return void
     */
    public function setHeight(?float $height): void;

    /**
     * Getter for Qty.
     *
     * @return int|null
     */
    public function getQty(): ?int;

    /**
     * Setter for Qty.
     *
     * @param int|null $qty
     *
     * @return void
     */
    public function setQty(?int $qty): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface|null
     */
    public function getExtensionAttributes(): ?CustomerElementExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ?\BelVG\MeasurementTool\Api\Data\CustomerElementExtensionInterface $extensionAttributes
    ): static;
}
