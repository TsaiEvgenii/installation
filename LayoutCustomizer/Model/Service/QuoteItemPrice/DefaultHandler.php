<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model\Service\QuoteItemPrice;

use BelVG\LayoutCustomizer\Api\Service\QuoteItemPrice\HandlerInterface;

class DefaultHandler implements HandlerInterface
{
    protected $basePriceCalculator;

    public function __construct(
        \BelVG\LayoutCustomizer\Model\Helper\PriceCalculator $priceCalculator
    ) {
        $this->basePriceCalculator = $priceCalculator;
    }

    public function isActive(): bool
    {
        return true;
    }

    public function isFit(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ): bool {
        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        $finalPrice = $this->basePriceCalculator->getCustomBasePrice($quoteItem);
        $finalPrice = $this->_applyOptionsPrice($quoteItem, $finalPrice);
        $finalPrice = max(0, $finalPrice);

        return $finalPrice; //convert happens in \BelVG\LayoutCustomizer\Model\ResourceModel\Layout::getLayoutData
    }

    private function _applyOptionsPrice(
        \Magento\Quote\Model\Quote\Item $quoteItem,
                                        $finalPrice
    ) {
        $product = $quoteItem->getProduct();
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);

                    //$finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                    $finalPrice += $group->getQuoteItemOptionPrice($confItemOption->getValue(), $basePrice, $quoteItem);
                }
            }
        }

        return $finalPrice;
    }
}
