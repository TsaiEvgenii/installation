<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Helper\Product;


use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

class HideOptions
{
    public function afterGetCustomOptions(
        Configuration $source,
        $result,
        ItemInterface $item
    ) {
        return array_filter($result, function ($option) {
            if (isset($option['hidden']) && $option['hidden'] === true) {
                return false;
            }
            return true;
        });
    }
}