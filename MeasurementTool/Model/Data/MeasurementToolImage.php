<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\MeasurementTool\Model\Data;

use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class MeasurementToolImage extends AbstractExtensibleModel implements MeasurementToolImageInterface
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
     * Getter for Img.
     *
     * @return string|null
     */
    public function getImg(): ?string
    {
        return $this->getData(self::IMG);
    }

    /**
     * Setter for Img.
     *
     * @param string|null $img
     *
     * @return void
     */
    public function setImg(?string $img): void
    {
        $this->setData(self::IMG, $img);
    }

    /**
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementtoolImageExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\BelVG\MeasurementTool\Api\Data\MeasurementtoolImageExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementtoolImageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): static
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
