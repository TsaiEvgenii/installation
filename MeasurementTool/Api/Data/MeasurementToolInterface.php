<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface MeasurementToolInterface extends ExtensibleDataInterface
{
    /**
     * String constants for property names
     */
    public const ENTITY_ID = "entity_id";
    public const NAME = "name";
    public const DESCRIPTION = "description";
    public const CUSTOMER_ID = "customer_id";
    public const ROOMS = "rooms";
    public const IMAGES = "images";
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
     * Getter for Customer Id.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Setter for Customer Id.
     *
     * @param int|null $customerId
     *
     * @return void
     */
    public function setCustomerId(?int $customerId): void;

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
     * Getter for Description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Setter for Description.
     *
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description): void;

    /**
     * Getter for Rooms.
     *
     * @return \BelVG\MeasurementTool\Api\Data\RoomInterface[]|null
     */
    public function getRooms(): ?array;

    /**
     * Setter for Rooms.
     *
     * @param \BelVG\MeasurementTool\Api\Data\RoomInterface[]|null $rooms
     *
     * @return void
     */
    public function setRooms(?array $rooms): void;

    /**
     * Getter for Images.
     *
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementtoolImageInterface[]|null
     */
    public function getImages(): ?array;

    /**
     * Setter for Images.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface[]|null $images
     *
     * @return void
     */
    public function setImages(?array $images): void;

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
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface|null
     */
    public function getExtensionAttributes(): ?MeasurementToolExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ?\BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface $extensionAttributes
    ): static;
}
