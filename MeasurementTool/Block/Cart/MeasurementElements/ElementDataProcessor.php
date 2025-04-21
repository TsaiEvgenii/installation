<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\Cart\MeasurementElements;


use BelVG\MeasurementTool\Api\ElementRepositoryInterface;
use BelVG\MeasurementTool\Model\ResourceModel\CustomerElement\CollectionFactory;
use BelVG\MeasurementTool\Model\ResourceModel\CustomerElement\Collection;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;

class ElementDataProcessor implements LayoutProcessorInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::ElementDataProcessor]: ';

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function process(array $jsLayout): array
    {
        try {
//            Use it to change some structure or data
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $jsLayout;
    }
}