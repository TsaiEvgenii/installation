<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model\Data;

use BelVG\InstallationElements\Api\Data\InstallationInterface;
use Magento\Framework\DataObject;
use BelVG\InstallationElements\Api\Data\AdditionalPriceInterface;

class Installation extends DataObject implements InstallationInterface
{
    /**
     * Getter for DisposalOfConstructionWaste.
     *
     * @return bool|null
     */
    public function getDisposalOfConstructionWaste(): ?bool
    {
        return $this->getData(self::DISPOSAL_OF_CONSTRUCTION_WASTE) === null ? null
            : (bool)$this->getData(self::DISPOSAL_OF_CONSTRUCTION_WASTE);
    }

    /**
     * Setter for DisposalOfConstructionWaste.
     *
     * @param bool|null $disposalOfConstructionWaste
     *
     * @return void
     */
    public function setDisposalOfConstructionWaste(?bool $disposalOfConstructionWaste): void
    {
        $this->setData(self::DISPOSAL_OF_CONSTRUCTION_WASTE, $disposalOfConstructionWaste);
    }

    public function getConditionsApproved(): ?bool
    {
        return $this->getData(self::CONDITIONS_APPROVED) === null ? null
            : (bool)$this->getData(self::CONDITIONS_APPROVED);
    }

    public function setConditionsApproved(?bool $conditionsApproved): void
    {
        $this->setData(self::CONDITIONS_APPROVED, $conditionsApproved);
    }
    /**
     * Getter for InternalFinish.
     *
     * @return bool|null
     */
    public function getInternalFinish(): ?bool
    {
        return $this->getData(self::INTERNAL_FINISH) === null ? null
            : (bool)$this->getData(self::INTERNAL_FINISH);
    }

    /**
     * Setter for InternalFinish.
     *
     * @param bool|null $internalFinish
     *
     * @return void
     */
    public function setInternalFinish(?bool $internalFinish): void
    {
        $this->setData(self::INTERNAL_FINISH, $internalFinish);
    }
    /**
     * Getter for InternalFinishType.
     *
     * @return string|null
     */
    public function getInternalFinishType(): ?string{
        return $this->getData(self::INTERNAL_FINISH_TYPE) === null ? null
            : (string)$this->getData(self::INTERNAL_FINISH_TYPE);

    }

    /**
     * Setter for InternalFinishType.
     *
     * @param string|null $internalFinishType
     *
     * @return void
     */
    public function setInternalFinishType(?string $internalFinishType): void
    {
        $this->setData(self::INTERNAL_FINISH_TYPE, $internalFinishType);
    }

    /**
     * Getter for InstallationLivingRoomQty.
     *
     * @return int|null
     */
    public function getInstallationLivingRoomQty(): ?int
    {
        return $this->getData(self::INSTALLATION_LIVING_ROOM_QTY) === null ? null
            : (int)$this->getData(self::INSTALLATION_LIVING_ROOM_QTY);
    }

    /**
     * Setter for InstallationLivingRoomQty.
     *
     * @param int|null $installationLivingRoomQty
     *
     * @return void
     */
    public function setInstallationLivingRoomQty(?int $installationLivingRoomQty): void
    {
        $this->setData(self::INSTALLATION_LIVING_ROOM_QTY, $installationLivingRoomQty);
    }

    /**
     * Getter for InstallationHighGroundFloorQty.
     *
     * @return int|null
     */
    public function getInstallationHighGroundFloorQty(): ?int
    {
        return $this->getData(self::INSTALLATION_HIGH_GROUND_FLOOR_QTY) === null ? null
            : (int)$this->getData(self::INSTALLATION_HIGH_GROUND_FLOOR_QTY);
    }

    /**
     * Setter for InstallationHighGroundFloorQty.
     *
     * @param int|null $installationHighGroundFloorQty
     *
     * @return void
     */
    public function setInstallationHighGroundFloorQty(?int $installationHighGroundFloorQty): void
    {
        $this->setData(self::INSTALLATION_HIGH_GROUND_FLOOR_QTY, $installationHighGroundFloorQty);
    }

    /**
     * Getter for InstallationFirstFloorQty.
     *
     * @return int|null
     */
    public function getInstallationFirstFloorQty(): ?int
    {
        return $this->getData(self::INSTALLATION_FIRST_FLOOR_QTY) === null ? null
            : (int)$this->getData(self::INSTALLATION_FIRST_FLOOR_QTY);
    }

    /**
     * Setter for InstallationFirstFloorQty.
     *
     * @param int|null $installationFirstFloorQty
     *
     * @return void
     */
    public function setInstallationFirstFloorQty(?int $installationFirstFloorQty): void
    {
        $this->setData(self::INSTALLATION_FIRST_FLOOR_QTY, $installationFirstFloorQty);
    }

    /**
     * Getter for InstallationAboveFirstFloorQty.
     *
     * @return int|null
     */
    public function getInstallationAboveFirstFloorQty(): ?int
    {
        return $this->getData(self::INSTALLATION_ABOVE_FIRST_FLOOR_QTY) === null ? null
            : (int)$this->getData(self::INSTALLATION_ABOVE_FIRST_FLOOR_QTY);
    }

    /**
     * Setter for InstallationAboveFirstFloorQty.
     *
     * @param int|null $installationAboveFirstFloorQty
     *
     * @return void
     */
    public function setInstallationAboveFirstFloorQty(?int $installationAboveFirstFloorQty): void
    {
        $this->setData(self::INSTALLATION_ABOVE_FIRST_FLOOR_QTY, $installationAboveFirstFloorQty);
    }

    /**
     * @return AdditionalPriceInterface[]|null
     */
    public function getAdditionalPrices(): ?array
    {
        return $this->getData(self::ADDITIONAL_PRICES) === null ? null
            : $this->getData(self::ADDITIONAL_PRICES);
    }

    /**
     * @param AdditionalPriceInterface[]|null $pricesData
     *
     * @return void
     */
    public function setAdditionalPrices(?array $pricesData): void
    {
        $this->setData(self::ADDITIONAL_PRICES, $pricesData);
    }
}
