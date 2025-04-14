<?php
namespace BelVG\LayoutCustomizer\Model\Helper;

use BelVG\LayoutCustomizer\Api\Helper\QuoteItemOptionManagement;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout as LayoutResource;
use BelVG\LayoutCustomizer\Exception\QuoteItemLayoutNotExists;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class PriceCalculator implements \BelVG\LayoutCustomizer\Api\Helper\PriceCalculator
{
    public $quoteItemOptionManagement;
    public $layoutModel;
    public $storeManager;

    public function __construct(
        QuoteItemOptionManagement $quoteItemOptionManagement,
        LayoutResource $layoutModel,
        StoreManagerInterface $storeManager
    ) {
        $this->quoteItemOptionManagement = $quoteItemOptionManagement;
        $this->layoutModel = $layoutModel;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns custom price depends on layout size or standard FinalPrice
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomBasePrice(Quote\Item $quoteItem)
    {
        $dimensions = $this->quoteItemOptionManagement->getDimensions($quoteItem);
        if (!isset($dimensions['width']) || !isset($dimensions['height'])){
            return $quoteItem->getProduct()->getFinalPrice(); //default price: $quoteItem->getProduct()->getFinalPrice()
        }

        $layout_id = $quoteItem->getProduct()->getData(\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR);
//        if (!$layout_id) {
//            throw new QuoteItemLayoutNotExists(__('QuoteItem (id="%1", sku="%2") does not exist', $quoteItem->getId(), $quoteItem->getSku()));
//        }

        $store_id = $this->storeManager->getStore()->getId();
        $layout_data = $this->layoutModel->getLayoutData($layout_id, $store_id, $dimensions['width'], $dimensions['height']);

        if (isset($layout_data['total_price'])) {
            return $layout_data['total_price'];
        }

        return $quoteItem->getProduct()->getFinalPrice(); //return default price
    }
}
