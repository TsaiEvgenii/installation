<?php
/**
 * Config
 *
 * @copyright Copyright © 2024 BelVG. All rights reserved.
 * @author    tsai.evgenii@gmail.com
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\ViewModel;


use Magento\Framework\View\Element\Block\ArgumentInterface;
use BelVG\InstallationElements\Model\Service\Config as InstallationConfig;
use Psr\Log\LoggerInterface;

class Config implements ArgumentInterface
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::ConfigViewModel]: ';

    public function __construct(
        private readonly InstallationConfig $installationConfig,
        private readonly LoggerInterface $logger
    ) {
    }

    public function isEnabled(): bool
    {
        try {
            return (bool)$this->installationConfig->isEnabled();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return false;
    }
    public function getConditionsFile(){
        try {
            return $this->installationConfig->getConditionsFile();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return '';
    }
}