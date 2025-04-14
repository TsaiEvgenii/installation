<?php

namespace BelVG\LayoutCustomizer\Model\Service;

use BelVG\LayoutCustomizer\Api\Service\QuoteItemPrice\HandlerInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class QuoteItemPrice implements \BelVG\LayoutCustomizer\Api\Service\QuoteItemPriceInterface
{
    const LOG_PREFIX = '[BelVG_LayoutCustomizer::QuoteItemPrice]: ';

    private $logger;
    private $handlers;

    public function __construct(
        LoggerInterface $logger,
        array $handlers = []
    ) {
        $this->logger = $logger;
        $this->handlers = $this->getValidHandlers($handlers);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return float
     * @throws LocalizedException
     */
    public function getCustomPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        /** @var HandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->isFit($quoteItem) && $handler->isActive()) {
                return (float)$handler->getCustomPrice($quoteItem);
            }
        }
        unset($handler);

        throw new LocalizedException(__('There is no handler to get the custom price'));
    }

    private function getValidHandlers(iterable $items) :iterable
    {
        $validItems = [];
        foreach ($items as $item) {
            if ($item instanceof HandlerInterface) {
                $validItems[] = $item;
                continue;
            }

            $this->logger->warning(sprintf(
                self::LOG_PREFIX . ' class "%s" does not implement the "\BelVG\LayoutCustomizer\Api\Service\QuoteItemPrice\HandlerInterface" interface',
                get_class($item)
            ));
        }
        unset($item);

        return $validItems;
    }
}
