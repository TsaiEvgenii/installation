<?php
namespace BelVG\LayoutCustomizer\Plugin\Magento\Quote\Model\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \BelVG\LayoutCustomizer\Api\Service\QuoteItemPriceInterface as QuoteItemPriceService;

class ItemPlugin
{
    protected $basePriceCalculator;
    protected $priceCurrency;
    protected $quoteItemPriceService;

    public function __construct(
        \BelVG\LayoutCustomizer\Model\Helper\PriceCalculator $priceCalculator,
        PriceCurrencyInterface $priceCurrency,
        QuoteItemPriceService $quoteItemPriceService
    ) {
        $this->basePriceCalculator = $priceCalculator;
        $this->priceCurrency = $priceCurrency;
        $this->quoteItemPriceService = $quoteItemPriceService;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return float|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        return $this->quoteItemPriceService->getCustomPrice($quoteItem);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param $resultItem
     * @param $product
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSetProduct(\Magento\Quote\Model\Quote\Item $subject, $resultItem, $product)
    {
        $custom_price = $this->getCustomPrice($subject);

        $subject->setCustomPrice($custom_price);
        $subject->setOriginalCustomPrice($custom_price);
        $subject->getProduct()->setIsSuperMode(true);

        return $subject;
    }
}
