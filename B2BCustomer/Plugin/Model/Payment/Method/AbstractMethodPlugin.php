<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\Model\Payment\Method;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Sales\Model\Order;

class AbstractMethodPlugin
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    public function __construct(
        Config                          $config,
        CustomerCheck                   $customerCheck,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->config = $config;
        $this->customerCheck = $customerCheck;
        $this->customerSession = $customerSession;
    }


    public function afterIsAvailable($subject, $result, $quote = null)
    {
        if ($this->customerSession->isLoggedIn()) {
            if ($this->customerCheck->isB2BCustomer($this->customerSession->getCustomer()->getGroupId(), $this->customerSession->getCustomer()->getStoreId())) {
                $paymentMethod = $subject->getCode();

                $allowedPaymentMethods = explode(',', $this->config->getPaymentMethods() ?? '');
                if ($allowedPaymentMethods && $paymentMethod && array_search($paymentMethod, $allowedPaymentMethods) === false) {
                    $result = false;
                }
            }
        }
        return $result;
    }

}
