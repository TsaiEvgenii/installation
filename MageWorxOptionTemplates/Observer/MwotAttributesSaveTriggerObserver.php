<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\MageWorxOptionTemplates\Observer;

use BelVG\MageWorxOptionTemplates\Helper\Config as MageWorxConfig;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class MwotAttributesSaveTriggerObserver implements ObserverInterface
{
    private const LOG_PREFIX = '[BelVG_MageWorxOptionTemplates::ProductSaveAfter]: ';

    public function __construct(
        private readonly MageWorxConfig $mageworxConfig,
        private readonly ManagerInterface $eventManager,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Run the same logic as \MageWorx\OptionBase\Observer\ProductSaveAfter::execute (but on condition)
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->mageworxConfig->getConfig(MageWorxConfig::UPDATE_OPTIONS)) {
                $product = $observer->getProduct();
                $this->eventManager->dispatch(
                    'mageworx_attributes_save_trigger',
                    ['product' => $product, 'after_template' => false]
                );
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
    }
}
