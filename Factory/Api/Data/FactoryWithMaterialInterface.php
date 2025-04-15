<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\Factory\Api\Data;

interface FactoryWithMaterialInterface
{
    /**
     * String constants for property names
     */
    const FACTORY_ID = "factory_id";
    const MATERIAL_ID = "material_id";
    const MATERIAL_IDENTIFIER = "material_identifier";
    const STORE_ID = "store_id";
    const PRIORITY = "priority";

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
     * Getter for MaterialId.
     *
     * @return int|null
     */
    public function getMaterialId(): ?int;

    /**
     * Setter for MaterialId.
     *
     * @param int|null $materialId
     *
     * @return void
     */
    public function setMaterialId(?int $materialId): void;

    /**
     * Getter for MaterialIdentifier.
     *
     * @return string|null
     */
    public function getMaterialIdentifier(): ?string;

    /**
     * Setter for MaterialIdentifier.
     *
     * @param string|null $materialIdentifier
     *
     * @return void
     */
    public function setMaterialIdentifier(?string $materialIdentifier): void;

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
     * Getter for Priority.
     *
     * @return int|null
     */
    public function getPriority(): ?int;

    /**
     * Setter for Priority.
     *
     * @param int|null $priority
     *
     * @return void
     */
    public function setPriority(?int $priority): void;
}
