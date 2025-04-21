<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
interface MeasurementToolImageInterface extends ExtensibleDataInterface
{
    /**
     * String constants for property names
     */
    public const ENTITY_ID = "entity_id";
    public const MEASUREMENT_TOOL_ID = "measurement_tool_id";
    public const IMG = "img";

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
     * Getter for Img.
     *
     * @return string|null
     */
    public function getImg(): ?string;

    /**
     * Setter for Img.
     *
     * @param string|null $img
     *
     * @return void
     */
    public function setImg(?string $img): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolImageExtensionInterface|null
     */
    public function getExtensionAttributes(): ?MeasurementToolImageExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ?\BelVG\MeasurementTool\Api\Data\MeasurementToolImageExtensionInterface $extensionAttributes
    ): static;
}
