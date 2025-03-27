<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Api\Data\AdditionalPriceInterface;
use BelVG\InstallationElements\Model\Service\RetrieveAdditionalPricesFromCustomOptions\RetrieveAdditionalPricesFromCustomOptionsProcessorInterface;

class RetrieveAdditionalPricesFromCustomOptions
{
    /**
     * @param RetrieveAdditionalPricesFromCustomOptionsProcessorInterface[] $processorsPool
     * @param AdditionalPriceInterface[] $additionalPrices
     */
    public function __construct(
        protected array $processorsPool = [],
        protected array $additionalPrices = []
    )
    {
    }

    public function retrieve($customOptions): array
    {
        foreach ($this->processorsPool as $processor){
            $this->additionalPrices = [
                ...$this->additionalPrices,
                ...$processor->process($customOptions)
            ];

        }

        return $this->additionalPrices;
    }
}