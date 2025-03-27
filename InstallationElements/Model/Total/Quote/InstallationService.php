<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Total\Quote;


use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use BelVG\InstallationElements\Model\Config\InstallationProductConfig;

class InstallationService extends AbstractTotal
{
    public const COLLECTOR_TYPE_CODE = 'installation_service';
    public const COLLECTOR_TYPE_TITLE = 'Installation service';

    public function __construct(
        private readonly InstallationProductConfig $installationProductConfig
    ) {
    }



    public function fetch(
        Quote $quote,
        Total $total
    ): ?array {
        if (!$quote->getItems()) {
            return null;
        }

        $installationServicePriceInclTax = 0;
        $installationServicePriceExclTax = 0;
        foreach ($quote->getItems() as $item) {
            if ($item->getProduct()->getSku() === $this->installationProductConfig->getProductSku()) {
                $installationServicePriceInclTax = $item->getData('price_incl_tax');
                $installationServicePriceExclTax = $item->getData('row_total');
            }
        }
        if ($installationServicePriceInclTax) {
            return [
                'code'  => self::COLLECTOR_TYPE_CODE,
                'title' => __(self::COLLECTOR_TYPE_TITLE),
                'value' => $installationServicePriceInclTax,
                'value_incl_tax' => $installationServicePriceInclTax,
                'value_excl_tax' => $installationServicePriceExclTax
            ];
        }

        return null;
    }

}
