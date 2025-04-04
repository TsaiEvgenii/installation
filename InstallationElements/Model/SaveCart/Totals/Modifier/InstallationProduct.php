<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\SaveCart\Totals\Modifier;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use BelVG\SaveCartTotals\Api\Totals\Modifiers\ModifierInterface;
use MageKey\SaveCart\Model\Item\Cart as SavedItemCart;

class InstallationProduct implements ModifierInterface
{
    public function __construct(
        private readonly InstallationProductConfig $installationProductConfig
    ) {
    }

    public function modifyCartTotals(SavedItemCart $cart, iterable $totals): iterable
    {
        $installationProductData = [];
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getSku() === $this->installationProductConfig->getProductSku()) {
                $item->getProduct();
            }
        }

        return array_merge($installationProductData, $totals);
    }
}