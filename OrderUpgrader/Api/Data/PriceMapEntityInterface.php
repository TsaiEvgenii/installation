<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\OrderUpgrader\Api\Data;

interface PriceMapEntityInterface
{
    /**
     * String constants for property names
     */
    public const ID = "id";
    public const PRICE = "price";

    /**
     * Getter for Id.
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Setter for Id.
     *
     * @param string|null $id
     *
     * @return void
     */
    public function setId(?string $id): void;

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
}
