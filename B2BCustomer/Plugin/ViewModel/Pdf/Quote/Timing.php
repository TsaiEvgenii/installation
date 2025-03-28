<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\ViewModel\Pdf\Quote;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;

class Timing
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;


    /**
     * @param Config $config
     * @param CustomerCheck $customerCheck
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config                $config,
        CustomerCheck         $customerCheck,
        CustomerSession       $customerSession,
        StoreManagerInterface $storeManager
    )
    {
        $this->config = $config;
        $this->customerCheck = $customerCheck;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @param $subject
     * @param $result
     * @return \DateTime|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetDeadlineDate($subject, $result)
    {
        if ($this->customerSession->isLoggedIn()) {
            if ($this->customerCheck->isB2BCustomer($this->customerSession->getCustomer()->getGroupId(), $this->customerSession->getCustomer()->getStoreId())) {
                $quote = $subject->getQuote();
                $store = $this->storeManager->getStore($quote->getStoreId());
                $deadlineConfig = (int)$this->config->getOfferDeadline($store->getWebsiteId());
                if ($deadlineConfig) {
                    $cartDate = new \DateTime($quote->getUpdatedAt());
                    $cartDate->modify('+'.$deadlineConfig.' day');

                    return $cartDate;
                }
            }
        }
        return $result;
    }
}
