<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Order;

use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;

class B2BDiscount extends \Magento\Framework\View\Element\AbstractBlock
{

    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();

        if ($order->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY) != 0) {
            $b2bDiscount = new \Magento\Framework\DataObject([
                'code' => B2BDiscountTotals::B2B_DISCOUNT_KEY,
                'strong' => false,
                'label' => __('B2B discount (%1%)', (int)$order->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY)),
                'value' => '-' . $order->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY)
            ]);

            $orderTotalsBlock->addTotal($b2bDiscount, 'subtotal');
        }

        return $this;
    }
}
