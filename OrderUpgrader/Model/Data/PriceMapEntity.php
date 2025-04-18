<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\OrderUpgrader\Model\Data;

use BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface;
use Magento\Framework\DataObject;

class PriceMapEntity extends DataObject implements PriceMapEntityInterface
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
     *
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->setData(self::ID, $id);
    }

    /**
     * Getter for Price.
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->getData(self::PRICE) === null ? null
            : (float)$this->getData(self::PRICE);
    }

    /**
     * Setter for Price.
     *
     * @param float|null $price
     *
     * @return void
     */
    public function setPrice(?float $price): void
    {
        $this->setData(self::PRICE, $price);
    }
}
