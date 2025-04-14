<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Api\Service\QuoteItemPrice;

interface HandlerInterface
{
    public function isActive(): bool;

    public function isFit(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ): bool;

    public function getCustomPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    );
}
