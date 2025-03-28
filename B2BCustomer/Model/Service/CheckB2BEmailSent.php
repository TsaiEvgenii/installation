<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\B2BCustomer\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CheckB2BEmailSent
{

    const B2B_EMAIL_SENT = 'b2b_email_sent_';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        CustomerRepositoryInterface $customerRepository,
        CustomerCheck $customerCheck
    )
    {
        $this->customerCheck = $customerCheck;
        $this->customerRepository = $customerRepository;
        $this->config = $config;
    }


    /**
     * @param $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAllowed($order): bool
    {
        $invoicesCount = $order->getInvoiceCollection()->count();
        if (!$order->getPayment()->getAdditionalInformation(self::B2B_EMAIL_SENT . $invoicesCount)) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            $paymentsData = $customer->getCustomAttribute('b2b_split_payment_data');
            if ($paymentsData) {
                $paymentsData = json_decode($paymentsData->getValue(), true);
                if (isset($paymentsData[$order->getEntityId()][$invoicesCount]) && $paymentsData[$order->getEntityId()][$invoicesCount]) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
