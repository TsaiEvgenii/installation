<?php


namespace BelVG\LayoutCustomizer\Api\Helper;


interface QuoteItemOptionManagement
{
    const OPT_PREFIX = 'option_';

    public function getOptionValueByMageworxId(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $mageworx_group_option_id
    );

    public function getOptions(
        int $quoteItemId
    ) :?iterable;

    public function getQuoteItemWidth(
        \Magento\Quote\Model\Quote\Item $quoteItem
    );

    public function getQuoteItemHeight(
        \Magento\Quote\Model\Quote\Item $quoteItem
    );

    public function getProductOptionValue(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $option_id
    );

    public function getDimensions(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $forceLoad = false
    );
}
