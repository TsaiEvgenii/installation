<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
namespace BelVG\OrderUpgrader\Model\Data;

use BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface;
use Magento\Framework\DataObject;

class MaterialUpgrade extends DataObject implements MaterialUpgradeInterface
{
    /**
     * Getter for Id.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData(self::ID);
    }

    /**
     * Setter for Id.
     *
     * @param string|null $id
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->setData(self::ID, $id);
    }

    /**
     * Getter for Label.
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->getData(self::LABEL);
    }

    /**
     * Setter for Label.
     *
     * @param string|null $label
     * @return void
     */
    public function setLabel(?string $label): void
    {
        $this->setData(self::LABEL, $label);
    }

    /**
     * Getter for MissingSku.
     *
     * @return string|null
     */
    public function getMissingSku(): ?string
    {
        return $this->getData(self::MISSING_SKU);
    }

    /**
     * Setter for MissingSku.
     *
     * @param string|null $missingSku
     * @return void
     */
    public function setMissingSku(?string $missingSku): void
    {
        $this->setData(self::MISSING_SKU, $missingSku);
    }

    /**
     * Getter for Image.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Setter for Image.
     *
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void
    {
        $this->setData(self::IMAGE, $image);
    }
}