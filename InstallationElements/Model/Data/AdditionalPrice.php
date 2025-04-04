<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Data;


use BelVG\InstallationElements\Api\Data\AdditionalPriceInterface;
use Magento\Framework\DataObject;

class AdditionalPrice extends DataObject  implements AdditionalPriceInterface
{

    public function getLabel(): ?string
    {
        return $this->getData(self::ADDITIONAL_PRICE_LABEL) === null ? null
            : (string)$this->getData(self::ADDITIONAL_PRICE_LABEL);
    }

    public function setLabel(?string $label): void
    {
        $this->setData(self::ADDITIONAL_PRICE_LABEL, $label);
    }

    public function getCode(): ?string
    {
        return $this->getData(self::ADDITIONAL_PRICE_CODE) === null ? null
            : (string)$this->getData(self::ADDITIONAL_PRICE_CODE);
    }

    public function setCode(?string $code): void
    {
        $this->setData(self::ADDITIONAL_PRICE_CODE, $code);
    }

    public function getPrice(): ?float
    {
        return $this->getData(self::ADDITIONAL_PRICE_PRICE) === null ? null
            : (float)$this->getData(self::ADDITIONAL_PRICE_PRICE);
    }

    public function setPrice(?float $price): void
    {
        $this->setData(self::ADDITIONAL_PRICE_PRICE, $price);
    }
}