<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Api\Webapi;

use BelVG\InstallationElements\Api\Data\InstallationInterface;

interface GetCalculatedPriceInterface
{
    /**
     * @param string                                                     $cartId
     * @param InstallationInterface $installationData
     *
     * @return string|null
     */
    public function getPrice(string $cartId, InstallationInterface $installationData): ?string;
}