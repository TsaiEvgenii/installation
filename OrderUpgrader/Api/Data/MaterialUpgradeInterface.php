<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
namespace BelVG\OrderUpgrader\Api\Data;

interface MaterialUpgradeInterface
{
    /**
     * String constants for property names
     */
    public const ID = "id";
    public const LABEL = "label";
    public const MISSING_SKU = "missing_sku";
    public const IMAGE = "image";

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
     * @return void
     */
    public function setId(?string $id): void;

    /**
     * Getter for Label.
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * Setter for Label.
     *
     * @param string|null $label
     * @return void
     */
    public function setLabel(?string $label): void;

    /**
     * Getter for MissingSku.
     *
     * @return string|null
     */
    public function getMissingSku(): ?string;

    /**
     * Setter for MissingSku.
     *
     * @param string|null $missingSku
     * @return void
     */
    public function setMissingSku(?string $missingSku): void;

    /**
     * Getter for Image.
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * Setter for Image.
     *
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void;
}