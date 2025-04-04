<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\MasterKey\Model\Service\MasterKeySku;
use Magento\Sales\Api\Data\OrderInterface;

class GetOrderQty
{
    public function __construct(
        protected InstallationProductConfig $installationProductConfig
    ) {
    }

    public function get(OrderInterface $order): int
    {
        $qty = 0;

        foreach ($order->getItems() as $item) {
            if (in_array($item->getSku(), $this->getExcludedProductsSkus())) {
                continue;
            }
            $qty += $item->getQtyOrdered();
        }
        return (int)$qty;
    }

    public function getExcludedProductsSkus(): array
    {
        return [
            $this->installationProductConfig->getProductSku(),
            MasterKeySku::D12->value,
            MasterKeySku::FIX600->value,
            MasterKeySku::PVC->value
        ];
    }

}