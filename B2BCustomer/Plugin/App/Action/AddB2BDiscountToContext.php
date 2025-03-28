<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Plugin\App\Action;


use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Psr\Log\LoggerInterface;

class AddB2BDiscountToContext
{
    public function __construct(
        protected CustomerSession $customerSession,
        protected HttpContext $httpContext,
        protected LoggerInterface $logger
    ){

    }

    public function beforeExecute(ActionInterface $subject)
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $discount = $customer->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY);
            if ($discount) {
                $this->httpContext->setValue(
                    B2BDiscountTotals::B2B_DISCOUNT_KEY,
                    (float)$discount,
                    0
                );
            }
        }

        return null;
    }

}