<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

namespace BelVG\OrderUpgrader\Api\Data;

interface UpgradeParameterInterface
{
    /**
     * String constants for property names
     */
    public const CODE = "code";
    public const VALUE = "value";

    /**
     * Getter for Code.
     *
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Setter for Code.
     *
     * @param string|null $code
     *
     * @return void
     */
    public function setCode(?string $code): void;

    /**
     * Getter for Value.
     *
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * Setter for Value.
     *
     * @param string|null $value
     *
     * @return void
     */
    public function setValue(?string $value): void;
}
