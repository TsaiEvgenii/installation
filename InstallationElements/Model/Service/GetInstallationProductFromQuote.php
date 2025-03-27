<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class GetInstallationProductFromQuote
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig
    ) {
    }

    public function get(Quote $quote): ?QuoteItem
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($this->isInstallation($item)) {
                return $item;
            }
        }

        return null;
    }

    public function isInstallation(QuoteItem $item): bool
    {
        return $item->getSku()
            && $item->getSku() === $this->installationProductConfig->getProductSku();
    }
}