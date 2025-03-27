<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\MasterAccountDiscount;


class InstallationServiceValidator extends \BelVG\InstallationElements\Model\SalesRule\InstallationServiceValidator
{

    public function getMessages(): array|\Magento\Framework\Phrase
    {
        return __('Master Account discount is not available for the Installation Service product');
    }

}