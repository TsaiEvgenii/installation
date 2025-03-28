<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\B2BCustomer\Model\Config;
use Magento\Customer\Api\CustomerRepositoryInterface;

class PartialInvoiceService
{

    const B2B_SPLIT_PAYMENT = 'b2b_split_payment_';
    const B2B_SPLIT_PERIOD = 'b2b_split_period_';



    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
    )
    {
        $this->customerRepository = $customerRepository;
    }


    public function isAllowed($order, $index): bool
    {
        $customer = $this->customerRepository->getById($order->getCustomerId());
        if ($customer->getCustomAttribute(self::B2B_SPLIT_PAYMENT . $index)) {
            return true;
        }
        return false;
    }
}
