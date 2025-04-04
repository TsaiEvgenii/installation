<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\B2BDiscount;

class InstallationServiceValidator extends \BelVG\InstallationElements\Model\SalesRule\InstallationServiceValidator
{

    public function getMessages(): array|\Magento\Framework\Phrase
    {
        return __('B2B discount is not available for the Installation Service product');
    }

}
