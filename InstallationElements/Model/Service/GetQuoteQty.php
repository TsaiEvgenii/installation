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
use Magento\Quote\Api\Data\CartInterface;

class GetQuoteQty
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig
    ) {
    }

    public function get(CartInterface|Quote $quote): int
    {
        $qty = 0;

        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getSku(), $this->getExcludedProductsSkus())) {
                continue;
            }
            $qty += $item->getQty();
        }
        return (int)$qty;
    }

    public function getExcludedProductsSkus(): array
    {
        return [
            $this->installationProductConfig->getProductSku()
        ];
    }

}