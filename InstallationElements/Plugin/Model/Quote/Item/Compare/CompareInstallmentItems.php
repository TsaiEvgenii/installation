<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\Quote\Item\Compare;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Compare;

class CompareInstallmentItems
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig
    ) {
    }

    public function afterCompare(
        Compare $source,
        bool $result,
        Item $target,
        Item $compared
    ): bool {
        if (
            $target->getSku() === $this->installationProductConfig->getProductSku()
            && $compared->getSku() === $this->installationProductConfig->getProductSku()
        ) {
            return true;
        }

        return $result;
    }
}