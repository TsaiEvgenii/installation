<?php


namespace BelVG\LayoutCustomizer\Api\Service;

interface QuoteItemPriceInterface
{
    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return float
     */
    public function getCustomPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    );
}
