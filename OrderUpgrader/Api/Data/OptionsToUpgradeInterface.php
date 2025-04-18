<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\Api\Data;


interface OptionsToUpgradeInterface
{
    public const OPTIONS = "options";
    public const MATERIALS_MAP = "materials_map";
    public const PRICE_MAP = "price_map";


    /**
     * Getter for Options.
     *
     * @return array|null
     */
    public function getOptions(): ?array;

    /**
     * Setter for Options.
     *
     * @param array|null $options
     *
     * @return void
     */
    public function setOptions(?array $options): void;

    /**
     * Getter for Materials Map.
     *
     * @return \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface[]|null
     */
    public function getMaterialsMap(): ?array;

    /**
     * Setter for Materials
     *
     * @param \BelVG\OrderUpgrader\Api\Data\MaterialUpgradeInterface[]|null $materialsMap
     *
     * @return void
     */
    public function setMaterialsMap(?array $materialsMap): void;

    /**
     * Getter for Materials Map.
     *
     * @return \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface[]|null
     */
    public function getPriceMap(): ?array;

    /**
     * Setter for Materials
     *
     * @param \BelVG\OrderUpgrader\Api\Data\PriceMapEntityInterface[]|null $priceMap
     *
     * @return void
     */
    public function setPriceMap(?array $priceMap): void;
}