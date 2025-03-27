<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\SalesRule;

use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Quote\Model\Quote\Item\AbstractItem;


class InstallationServiceValidator implements \Laminas\Validator\ValidatorInterface
{
    public function __construct(
        private readonly InstallationProductConfig $installationProductConfig,
    ) {

    }

    public function isValid($value): bool
    {
        return $value->getSku()
            && $value->getSku() !== $this->installationProductConfig->getProductSku();
    }

    public function getMessages(): array|\Magento\Framework\Phrase
    {
        return __('Discounts are not available for the Installation Service product');
    }
}