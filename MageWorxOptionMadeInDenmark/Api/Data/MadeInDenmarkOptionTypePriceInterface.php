<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

namespace BelVG\MageWorxOptionMadeInDenmark\Api\Data;

interface MadeInDenmarkOptionTypePriceInterface
{
    /**
     * String constants for property names
     */
    public const OPTION_TYPE_PRICE_ID = "option_type_price_id";
    public const OPTION_TYPE_ID = "option_type_id";
    public const FACTORY_ID = "factory_id";
    public const STORE_ID = "store_id";
    public const PRICE = "price";
    public const PRICE_TYPE = "price_type";

    /**
     * Getter for OptionTypePriceId.
     *
     * @return int|null
     */
    public function getOptionTypePriceId(): ?int;

    /**
     * Setter for OptionTypePriceId.
     *
     * @param int|null $optionTypePriceId
     *
     * @return void
     */
    public function setOptionTypePriceId(?int $optionTypePriceId): void;

    /**
     * Getter for OptionTypeId.
     *
     * @return int|null
     */
    public function getOptionTypeId(): ?int;

    /**
     * Setter for OptionTypeId.
     *
     * @param int|null $optionTypeId
     *
     * @return void
     */
    public function setOptionTypeId(?int $optionTypeId): void;

    /**
     * Getter for FactoryId.
     *
     * @return int|null
     */
    public function getFactoryId(): ?int;

    /**
     * Setter for FactoryId.
     *
     * @param int|null $factoryId
     *
     * @return void
     */
    public function setFactoryId(?int $factoryId): void;

    /**
     * Getter for StoreId.
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * Setter for StoreId.
     *
     * @param int|null $storeId
     *
     * @return void
     */
    public function setStoreId(?int $storeId): void;

    /**
     * Getter for Price.
     *
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * Setter for Price.
     *
     * @param float|null $price
     *
     * @return void
     */
    public function setPrice(?float $price): void;

    /**
     * Getter for PriceType.
     *
     * @return string|null
     */
    public function getPriceType(): ?string;

    /**
     * Setter for PriceType.
     *
     * @param string|null $priceType
     *
     * @return void
     */
    public function setPriceType(?string $priceType): void;
}
