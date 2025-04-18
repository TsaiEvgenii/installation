<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\OrderUpgrader\ViewModel;


use BelVG\OrderUpgrader\Model\Service\Config as OrderUpgraderConfig;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class Config implements ArgumentInterface
{
    private const LOG_PREFIX = '[BelVG_OrderUpgrader::ConfigViewModel]: ';

    public function __construct(
        private readonly OrderUpgraderConfig $orderUpgraderConfig,
        private readonly LoggerInterface $logger
    ) {
    }

    public function isEnabled(): bool
    {
        try {
            return (bool)$this->orderUpgraderConfig->isEnabled();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return false;
    }
}