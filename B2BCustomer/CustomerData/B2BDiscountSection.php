<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\CustomerData;


use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Psr\Log\LoggerInterface;

class B2BDiscountSection implements SectionSourceInterface
{

    public function __construct(
        protected CustomerSession $customerSession,
        protected HttpContext $httpContext,
        protected Config $config,
        protected LoggerInterface $logger
    ) {

    }

    public function getSectionData()
    {
        $data = [];
        $b2bDiscountValue = $this->httpContext->getValue(B2BDiscountTotals::B2B_DISCOUNT_KEY);
        if ($b2bDiscountValue) {
            $maxDiscount = $this->config->getDiscountMaxValueCart();
            $data[B2BDiscountTotals::B2B_DISCOUNT_KEY] = $b2bDiscountValue;
            $data['max_discount'] = $maxDiscount;
        }

        return $data;
    }
}