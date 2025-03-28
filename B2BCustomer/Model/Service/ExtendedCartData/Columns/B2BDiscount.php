<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service\ExtendedCartData\Columns;

use BelVG\Minicart\Api\Service\ExtendedItemData\QuoteColumnInterface;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Quote\Api\Data\CartInterface;

class B2BDiscount implements QuoteColumnInterface
{
    public function __construct(
        protected CheckoutHelper $checkoutHelper
    )
    {
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getKey()
    {
        return 'b2b_discount_data';
    }

    public function getContent(CartInterface $quote)
    {
        $result = null;
        if ($quote && (int)$quote->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY)) {
            $result = [
                'title' => __('B2B discount <b>(-%1%)</b>', (int)$quote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY)),
                'value' => '-' . $this->checkoutHelper->formatPrice($quote->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY))
            ];
        }

        return $result;
    }
}
