<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\Widget;

use BelVG\MeasurementTool\Model\Service\Config as MeasurementToolConfig;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;


class MeasurementToolEntryPoint extends Template implements BlockInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::MeasurementToolEntryPointWidget]: ';

    protected $_template = "widget/entry-point.phtml";

    public function __construct(
        protected MeasurementToolConfig $measurementToolConfig,
        protected UrlInterface $urlBuilder,
        protected LoggerInterface $logger,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isEnabled(): bool
    {
        return $this->measurementToolConfig->isEnabled();
    }

    public function getImage(): string
    {
        $imageLink = '';
        try {
            $imageLink = $this->measurementToolConfig->getWidgetImagePath();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $imageLink;
    }

    public function getLabel(): string
    {
        $label = '';
        try {

            $label = $this->measurementToolConfig->getWidgetLabel();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return $label;
    }

    public function getMeasurementToolPage(){
        return $this->urlBuilder->getUrl('measurement_tool');
    }

}