<?php

namespace BelVG\B2BCustomer\Plugin\Quote\Model\Address;

use BelVG\B2BCustomer\Model\Config;
use BelVG\MatrixRateProfile\Model\Service\StandardShipping as StandardShippingService;
use Magento\Customer\Model\Session as CustomerSession;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Psr\Log\LoggerInterface;

class RatePlugin
{

    const LOG_PREFIX = '[BelVG_B2BCustomer::ChangeShippingRate]: ';

    /**
     * @var StandardShippingService
     */
    protected $standardShippingService;


    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $config;

    /**
     * @param CustomerSession $customerSession
     * @param CustomerCheck $customerCheck
     * @param StandardShippingService $standardShippingService
     */
    public function __construct(
        CustomerSession         $customerSession,
        LoggerInterface         $logger,
        CustomerCheck           $customerCheck,
        Config                  $config,
        StandardShippingService $standardShippingService
    )
    {
        $this->standardShippingService = $standardShippingService;
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->customerCheck = $customerCheck;
        $this->logger = $logger;
    }


    /**
     * @param $subject
     * @param \Magento\Shipping\Model\Rate\Result $result
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function afterCollectRates(
        $subject,
        \Magento\Shipping\Model\Rate\Result $result,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    )
    {
        if ($this->customerSession->isLoggedIn()) {
            $originalRates = $result->getAllRates();
            $customer = $this->customerSession->getCustomer();
            try {
                if (
                    $this->customerCheck->isB2BCustomer($customer->getGroupId(), $customer->getStoreId()) &&
                    $this->config->getShippingDiscountEnabled($customer->getStore()->getId())
                ) {
                    $standardRate = $this->standardShippingService->findStandardShippingRate($request->getDestPostcode(), $request->getWebsiteId());
                    if ($standardRate && $standardRate->getPrice() != 0) {
                        $rates = $originalRates;
                        $result->reset();
                        foreach ($rates as $rate) {
                            if (($rate->getPrice() - $standardRate->getPrice() >= 0)) {
                                $rate->setPrice($rate->getPrice() - $standardRate->getPrice());
                            } else {
                                $rate->setPrice(0);
                            }
                            $result->append($rate);
                        }
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->alert(self::LOG_PREFIX . $exception->getMessage());
                $result->reset();
                foreach ($originalRates as $rate) {
                    $result->append($rate);
                }
            }
        }
        return $result;
    }
}
