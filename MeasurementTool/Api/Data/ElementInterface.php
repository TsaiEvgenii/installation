<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
interface ElementInterface extends ExtensibleDataInterface
{
    /**
     * String constants for property names
     */
    public const ENTITY_ID = "entity_id";
    public const TYPE = "type";
    public const IMG = "img";
    public const NAME = "name";
    public const WIDTH = "width";
    public const HEIGHT = "height";
    public const QTY = "qty";
    public const ROOM_ID = "room_id";
    public const RECORD_ID = "record_id";
    public const CREATED_AT = 'created_at';

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
     * Getter for Room Id.
     *
     * @return int|null
     */
    public function getRoomId(): ?int;

    /**
     * Setter for Room Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setRoomId(?int $id): void;

    /**
     * @return int|null
     */
    public function getRecordId(): ?int;

    /**
     * @param int|null $measurementToolId
     *
     * @return void
     */
    public function setRecordId(?int $measurementToolId): void;

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
     * Getter for Img.
     *
     * @return string|null
     */
    public function getImg(): ?string;

    /**
     * Setter for Name.
     *
     * @param string|null $img
     *
     * @return void
     */
    public function setImg(?string $img): void;

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
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created_at
     *
     * @param string|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \BelVG\MeasurementTool\Api\Data\ElementExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ElementExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\ElementExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ?\BelVG\MeasurementTool\Api\Data\ElementExtensionInterface $extensionAttributes
    ): static;
}
