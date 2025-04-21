<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block;


use BelVG\MeasurementTool\Model\CompositeConfigProvider;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{
    protected SerializerInterface $serializer;

    protected array $layoutProcessors;
    protected CompositeConfigProvider $configProvider;

    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        CompositeConfigProvider $configProvider,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
        $this->serializer = $serializer;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->configProvider = $configProvider;
    }
    public function getJsLayout(): bool|string
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return $this->serializer->serialize($this->jsLayout);
    }

    public function getMeasurementToolConfig(): array
    {
        return $this->configProvider->getConfig();
    }

    public function getSerializedMeasurementToolConfig(): bool|string
    {
        return  $this->serializer->serialize($this->getMeasurementToolConfig());
    }
}