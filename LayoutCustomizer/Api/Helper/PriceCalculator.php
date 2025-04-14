<?php


namespace BelVG\LayoutCustomizer\Api\Helper;


interface PriceCalculator
{
    public function getCustomBasePrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    );
}