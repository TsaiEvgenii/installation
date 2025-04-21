<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RoomInterface extends ExtensibleDataInterface
{
    /**
     * String constants for property names
     */
    public const ENTITY_ID = "entity_id";
    public const NAME = "name";
    public const MEASUREMENT_TOOL_ID = "measurement_tool_id";
    public const RECORD_ID = "record_id";
    public const ELEMENTS = "elements";
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
     * Getter for Elements.
     *
    /**
     * @return \BelVG\MeasurementTool\Api\Data\ElementInterface[]|null
     */
    public function getElements(): ?array;

    /**
     * Setter for Elements.
     *
     *
     * @param \BelVG\MeasurementTool\Api\Data\ElementInterface[]|null $elements
     *
     * @return void
     */
    public function setElements(?array $elements): void;

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
     * @return \BelVG\MeasurementTool\Api\Data\RoomExtensionInterface|null
     */
    public function getExtensionAttributes(): ?RoomExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\RoomExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ?\BelVG\MeasurementTool\Api\Data\RoomExtensionInterface $extensionAttributes
    ): static;
}
