<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Model;

use BelVG\AdditionalServices\Model\AbstractSubtotalWithoutServicesReader;
use BelVG\InstallationElements\Model\Total\Quote\InstallationService;

class SubtotalWithoutServicesReader extends AbstractSubtotalWithoutServicesReader
{
    public function getTotalServiceCode(): string
    {
        return InstallationService::COLLECTOR_TYPE_CODE;
    }
}