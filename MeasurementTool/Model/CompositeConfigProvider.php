<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


class CompositeConfigProvider implements ConfigProviderInterface
{

    /**
     * @var ConfigProviderInterface[] $configProviders
     */
    public function __construct(
       private readonly array $configProviders = []
    ) {}

    public function getConfig(): array
    {
        $config = [];
        foreach ($this->configProviders as $configProvider) {
            $config = array_merge_recursive($config, $configProvider->getConfig());
        }
        return $config;
    }
}