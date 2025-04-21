<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model\Data;

use BelVG\MeasurementTool\Api\Data\ElementInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Element extends AbstractExtensibleModel implements ElementInterface
{

    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID) === null ? null
            : (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getRoomId(): ?int
    {
        return $this->getData(self::ROOM_ID) === null ? null
            : (int)$this->getData(self::ROOM_ID);
    }

    /**
     * Setter for Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setRoomId(?int $id): void
    {
        $this->setData(self::ROOM_ID, $id);
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
     * @return \BelVG\MeasurementTool\Api\Data\ElementExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\BelVG\MeasurementTool\Api\Data\ElementExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\ElementExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): static
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
