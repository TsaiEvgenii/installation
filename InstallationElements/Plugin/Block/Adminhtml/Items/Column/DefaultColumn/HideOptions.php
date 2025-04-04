<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Block\Adminhtml\Items\Column\DefaultColumn;


use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;

class HideOptions
{
    public function afterGetOrderOptions(
        DefaultColumn $source,
        array $options
    ): array {

        return array_filter($options, function ($option) {
            if (isset($option['hidden']) && $option['hidden'] === true) {
                return false;
            }
            return true;
        });

    }
}