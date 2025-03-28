<?php

namespace BelVG\B2BCustomer\Observer;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class CustomerSaveBefore implements ObserverInterface
{
    const B2B_DISCOUNT_FIELD = 'b2b_discount';

    protected CustomerCheck $customerCheck;

    protected Config $config;

    public function __construct(
        Config $config,
        CustomerCheck $customerCheck
    )
    {
        $this->config = $config;
        $this->customerCheck = $customerCheck;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        if ($this->customerCheck->isB2BCustomer($customer->getGroupId(), $customer->getStoreId())) {
            $maxDiscount = $this->config->getDiscountMaxValue();
            if ($customer->getCustomAttribute(self::B2B_DISCOUNT_FIELD) && (int)$customer->getCustomAttribute(self::B2B_DISCOUNT_FIELD)->getValue() > (int)$maxDiscount) {
                throw new LocalizedException(
                    __('Discount can\'t be more than ' . $maxDiscount . '%')
                );
            }
        }
    }
}
