<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Model\Data;

use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class MeasurementTool extends AbstractExtensibleModel implements MeasurementToolInterface
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


    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID) === null ? null
            : (int)$this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(?int $customerId): void
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Getter for Name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME) === null ? null
            : (string)$this->getData(self::NAME);
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
     * Getter for Description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::NAME) === null ? null
            : (string)$this->getData(self::NAME);
    }

    /**
     * Setter for Description.
     *
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Getter for Rooms.
     *
     * @return \BelVG\MeasurementTool\Api\Data\RoomInterface[]|null
     */
    public function getRooms(): ?array
    {
        return $this->getData(self::ROOMS) === null ? null
            : $this->getData(self::ROOMS);
    }

    /**
     * Setter for Rooms.
     *
     * @param \BelVG\MeasurementTool\Api\Data\RoomInterface[]|null $rooms
     *
     * @return void
     */
    public function setRooms(?array $rooms): void
    {
        $this->setData(self::ROOMS, $rooms);
    }

    /**
     * Getter for Images.
     *
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementtoolImageInterface[]|null
     */
    public function getImages(): ?array
    {
        return $this->getData(self::IMAGES) === null ? null
            : $this->getData(self::IMAGES);
    }

    /**
     * Setter for Images.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface[]|null $images
     *
     * @return void
     */
    public function setImages(?array $images): void
    {
        $this->setData(self::IMAGES, $images);
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
     * @return \BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \BelVG\MeasurementTool\Api\Data\MeasurementToolExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): static
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
