<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Block\Total;


use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class WarningMessage extends AbstractCart
{
    /**
     * @var CustomerCheck
     */
    private CustomerCheck $customerCheck;

    /**
     * WarningMessage constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param CheckoutSession $checkoutSession
     * @param CustomerCheck $customerCheck
     * @param array $data
     */
    public function __construct(
        Context         $context,
        Session         $customerSession,
        CheckoutSession $checkoutSession,
        CustomerCheck   $customerCheck,
        array           $data = []
    )
    {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->customerCheck = $customerCheck;
    }

    public function getText()
    {
        return __('Discount amount is exceeded, b2b discount is lowered to maximum allowed value');
    }

    public function isShow(): bool
    {
        $quote = $this->getQuote();
        if ($quote !== null) {
            $customer = $quote->getCustomer();
            if (
                $customer->getGroupId()
                && $this->customerCheck->isB2BCustomer($customer->getGroupId(), $customer->getStoreId())
                && $customer->getCustomAttribute(B2BDiscountTotals::B2B_DISCOUNT_KEY)
            ) {
                $b2bDiscount = $customer->getCustomAttribute(B2BDiscountTotals::B2B_DISCOUNT_KEY)->getValue();
                if ($b2bDiscount > $quote->getData(B2BDiscountTotals::B2B_DISCOUNT_PERCENT_KEY)) {
                    return true;
                }
            }

        }
        return false;
    }

}
