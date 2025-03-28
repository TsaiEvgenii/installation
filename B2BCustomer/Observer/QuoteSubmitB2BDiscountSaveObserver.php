<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Observer;

use BelVG\B2BCustomer\Block\Adminhtml\Order\Total\B2BDiscount;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;

/**
 * Class QuoteSubmitB2BDiscountSaveObserver
 *
 * @package BelVG\B2BCustomer\Observer
 */
class QuoteSubmitB2BDiscountSaveObserver implements ObserverInterface
{

    public function execute(EventObserver $observer): void
    {
        $quote = $observer->getQuote();
        if ($quote->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY)) {
            $order = $observer->getOrder();
            $order->setData(B2BDiscountTotals::B2B_DISCOUNT_KEY, $quote->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY));
            $order->setData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY, $quote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY));
        }
    }
}
