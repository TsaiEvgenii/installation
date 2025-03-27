<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Api\Data;

interface AdditionalPriceInterface
{
    const ADDITIONAL_PRICE_LABEL = 'label';
    const ADDITIONAL_PRICE_CODE ='code';
    const ADDITIONAL_PRICE_PRICE ='price';

    /**
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * @param string|null $label
     *
     * @return void
     */
    public function setLabel(?string $label): void;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string|null $code
     *
     * @return void
     */
    public function setCode(?string $code): void;

    /**
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * @param float|null $value
     *
     * @return void
     */
    public function setPrice(?float $value): void;
}