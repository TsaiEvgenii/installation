<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

namespace BelVG\MageWorxOptionMadeInDenmark\Model\Data;

use BelVG\MageWorxOptionMadeInDenmark\Api\Data\MadeInDenmarkOptionTypePriceInterface;
use Magento\Framework\DataObject;

class MadeInDenmarkOptionTypePrice extends DataObject implements MadeInDenmarkOptionTypePriceInterface
{
    /**
     * Getter for OptionTypePriceId.
     *
     * @return int|null
     */
    public function getOptionTypePriceId(): ?int
    {
        return $this->getData(self::OPTION_TYPE_PRICE_ID) === null ? null
            : (int)$this->getData(self::OPTION_TYPE_PRICE_ID);
    }

    /**
     * Setter for OptionTypePriceId.
     *
     * @param int|null $optionTypePriceId
     *
     * @return void
     */
    public function setOptionTypePriceId(?int $optionTypePriceId): void
    {
        $this->setData(self::OPTION_TYPE_PRICE_ID, $optionTypePriceId);
    }

    /**
     * Getter for OptionTypeId.
     *
     * @return int|null
     */
    public function getOptionTypeId(): ?int
    {
        return $this->getData(self::OPTION_TYPE_ID) === null ? null
            : (int)$this->getData(self::OPTION_TYPE_ID);
    }

    /**
     * Setter for OptionTypeId.
     *
     * @param int|null $optionTypeId
     *
     * @return void
     */
    public function setOptionTypeId(?int $optionTypeId): void
    {
        $this->setData(self::OPTION_TYPE_ID, $optionTypeId);
    }

    /**
     * Getter for FactoryId.
     *
     * @return int|null
     */
    public function getFactoryId(): ?int
    {
        return $this->getData(self::FACTORY_ID) === null ? null
            : (int)$this->getData(self::FACTORY_ID);
    }

    /**
     * Setter for FactoryId.
     *
     * @param int|null $factoryId
     *
     * @return void
     */
    public function setFactoryId(?int $factoryId): void
    {
        $this->setData(self::FACTORY_ID, $factoryId);
    }

    /**
     * Getter for StoreId.
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID) === null ? null
            : (int)$this->getData(self::STORE_ID);
    }

    /**
     * Setter for StoreId.
     *
     * @param int|null $storeId
     *
     * @return void
     */
    public function setStoreId(?int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
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

    /**
     * Getter for PriceType.
     *
     * @return string|null
     */
    public function getPriceType(): ?string
    {
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * Setter for PriceType.
     *
     * @param string|null $priceType
     *
     * @return void
     */
    public function setPriceType(?string $priceType): void
    {
        $this->setData(self::PRICE_TYPE, $priceType);
    }
}
