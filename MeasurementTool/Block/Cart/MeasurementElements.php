<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\Cart;


use BelVG\MeasurementTool\Model\CompositeConfigProvider;
use BelVG\MeasurementTool\Block\Cart\MeasurementElements\LayoutProcessorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;

class MeasurementElements extends Template
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::MeasurementElementsBlock]: ';

    /**
     * @var LayoutProcessorInterface[]
     */
    protected array $layoutProcessors;

    public function __construct(
        private CompositeConfigProvider $configProvider,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
        Context $context,
        array $data = [],
        array $layoutProcessors = [],
    ) {
        $this->layoutProcessors = $layoutProcessors;
        parent::__construct($context, $data);
    }

    public function getJsLayout(): false|string
    {
        try {
            foreach ($this->layoutProcessors as $processor) {
                $this->jsLayout = $processor->process($this->jsLayout);
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return json_encode($this->jsLayout);
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