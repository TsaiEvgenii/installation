<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Api\Data\InstallationInterface;
use BelVG\InstallationElements\Api\Webapi\GetCalculatedPriceInterface;

class GetCalculatedPrice implements GetCalculatedPriceInterface
{
    public function __construct(
        private readonly InstallationPriceCalculator $installationPriceCalculator
    ){

    }
    public function getPrice(string $cartId, InstallationInterface $installationData): ?string
    {
        $priceData = $this->installationPriceCalculator->calculate($cartId, $installationData);
        return $priceData['price'];
    }
}