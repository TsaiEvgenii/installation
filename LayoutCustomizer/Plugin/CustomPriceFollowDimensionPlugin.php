<?php

/**
 * This plugin doesn't used and @todo: should be removed if we can use this way of price calculation
 *
 * We change plugin at the `quote_item` level in BelVG\LayoutCustomizer\Plugin\Magento\Quote\Model\Quote\ItemPlugin
 */


namespace BelVG\LayoutCustomizer\Plugin;


class CustomPriceFollowDimensionPlugin
{
    public static $skip_final_price_mod = false;

    private $quoteItemOptionManagement;
    private $quoteItemFactory;
    private $itemResourceModel;
    private $cartFactory;
    private $quoteFactory;

    public function __construct(
        \BelVG\LayoutCustomizer\Api\Helper\QuoteItemOptionManagement $quoteItemOptionManagement,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteItemOptionManagement = $quoteItemOptionManagement;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->cartFactory = $cartFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Validate product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function validate($product)
    {
        if (!$product->hasCustomOptions()) {
            return false;
        }

        $optionIds = $product->getCustomOption('option_ids');
        if (!$optionIds) {
            return false;
        }

        return true;
    }

    public function afterGetFinalPrice($subject, $result, $qty, $product)
    {
        if (!$this->validate($product)) {
            return $result;
        }

        $optionIds = $product->getCustomOption('option_ids');
        $itemId = $optionIds->getItemId();

        if (self::$skip_final_price_mod == true) {
            return $result;
        }
        $quote = $this->quoteFactory->create()->getQuote(); //->getAllVisibleItems()
        $quoteItem = $quote->getItemById($itemId);

/*
        $optionIds = $product->getCustomOption('option_ids');

        $itemId = $optionIds->getItemId();
        $quoteItem = $this->quoteItemFactory->create();
        $this->itemResourceModel->load($quoteItem, $itemId);

        $quote = $this->quoteFactory->create()->load($quoteItem->getQuoteId());
        $quoteItem->setQuote($quote);
        $quoteItem->setOptions($product->getOptions());
*/

        $dimensions = $this->quoteItemOptionManagement->getDimensions($quoteItem);
        if (!isset($dimensions['width']) || !isset($dimensions['height'])){
            return;
        }

        /**
         * logic of price cal should be there...
         */

        return $result;
    }
}
