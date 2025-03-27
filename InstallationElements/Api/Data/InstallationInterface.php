<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Api\Data;

interface InstallationInterface
{
    /**
     * String constants for property names
     */
    public const DISPOSAL_OF_CONSTRUCTION_WASTE = "disposal_of_construction_waste";
    public const INTERNAL_FINISH = "internal_finish";
    public const INTERNAL_FINISH_TYPE = "internal_finish_type";
    public const INSTALLATION_LIVING_ROOM_QTY = "installation_living_room_qty";
    public const INSTALLATION_HIGH_GROUND_FLOOR_QTY = "installation_high_ground_floor_qty";
    public const INSTALLATION_FIRST_FLOOR_QTY = "installation_first_floor_qty";
    public const INSTALLATION_ABOVE_FIRST_FLOOR_QTY = "installation_above_first_floor_qty";
    public const ADDITIONAL_PRICES = "additional_prices";
    public const CONDITIONS_APPROVED = "conditions_approved";

    /**
     * Getter for DisposalOfConstructionWaste.
     *
     * @return bool|null
     */
    public function getDisposalOfConstructionWaste(): ?bool;

    /**
     * Setter for DisposalOfConstructionWaste.
     *
     * @param bool|null $disposalOfConstructionWaste
     *
     * @return void
     */
    public function setDisposalOfConstructionWaste(?bool $disposalOfConstructionWaste): void;

    /**
     * Getter for ConditionsApproved.
     *
     * @return bool|null
     */
    public function getConditionsApproved(): ?bool;

    /**
     * Setter for ConditionsApproved.
     *
     * @param bool|null $conditionsApproved
     *
     * @return void
     */
    public function setConditionsApproved(?bool $conditionsApproved): void;

    /**
     * Getter for InternalFinish.
     *
     * @return bool|null
     */
    public function getInternalFinish(): ?bool;

    /**
     * Setter for InternalFinish.
     *
     * @param bool|null $internalFinish
     *
     * @return void
     */
    public function setInternalFinish(?bool $internalFinish): void;

    /**
     * Getter for InternalFinishType.
     *
     * @return string|null
     */
    public function getInternalFinishType(): ?string;

    /**
     * Setter for InternalFinishType.
     *
     * @param string|null $internalFinishType
     *
     * @return void
     */
    public function setInternalFinishType(?string $internalFinishType): void;

    /**
     * Getter for InstallationLivingRoomQty.
     *
     * @return int|null
     */
    public function getInstallationLivingRoomQty(): ?int;

    /**
     * Setter for InstallationLivingRoomQty.
     *
     * @param int|null $installationLivingRoomQty
     *
     * @return void
     */

    public function setInstallationLivingRoomQty(?int $installationLivingRoomQty): void;
    /**
     * Getter for InstallationHighGroundFloorQty.
     *
     * @return int|null
     */
    public function getInstallationHighGroundFloorQty(): ?int;

    /**
     * Setter for InstallationHighGroundFloorQty.
     *
     * @param int|null $installationHighGroundFloorQty
     *
     * @return void
     */
    public function setInstallationHighGroundFloorQty(?int $installationHighGroundFloorQty): void;

    /**
     * Getter for InstallationFirstFloorQty.
     *
     * @return int|null
     */
    public function getInstallationFirstFloorQty(): ?int;

    /**
     * Setter for InstallationFirstFloorQty.
     *
     * @param int|null $installationFirstFloorQty
     *
     * @return void
     */
    public function setInstallationFirstFloorQty(?int $installationFirstFloorQty): void;

    /**
     * Getter for InstallationAboveFirstFloorQty.
     *
     * @return int|null
     */
    public function getInstallationAboveFirstFloorQty(): ?int;

    /**
     * Setter for InstallationAboveFirstFloorQty.
     *
     * @param int|null $installationAboveFirstFloorQty
     *
     * @return void
     */
    public function setInstallationAboveFirstFloorQty(?int $installationAboveFirstFloorQty): void;

    /**
     * @return \BelVG\InstallationElements\Api\Data\AdditionalPriceInterface[]|null
     */
    public function getAdditionalPrices(): ?array;

    /**
     * @param \BelVG\InstallationElements\Api\Data\AdditionalPriceInterface[]|null $pricesData
     *
     * @return void
     */
    public function setAdditionalPrices(?array $pricesData): void;
}
