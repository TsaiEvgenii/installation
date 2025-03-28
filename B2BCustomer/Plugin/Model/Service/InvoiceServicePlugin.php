<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\Model\Service;

use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Psr\Log\LoggerInterface;

class InvoiceServicePlugin
{

    private const LOG_PREFIX = '[BelVG_B2BCustomer::InvoiceServicePlugin]: ';


    /**
     * @var CustomerCheck
     */
    protected CustomerCheck $customerCheck;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param CustomerCheck $customerCheck
     */
    public function __construct(
        LoggerInterface $logger,

        CustomerCheck   $customerCheck,

    )
    {
        $this->logger = $logger;
        $this->customerCheck = $customerCheck;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $order
     * @param $index
     * @param $customer
     * @param $captureCase
     * @return false|mixed
     */
    public function aroundExecute($subject, \Closure $proceed, $order, $index = null, $customer = null, $captureCase = null)
    {
        try {
            if ($this->customerCheck->isB2BCustomer($order->getCustomerGroupId(), $order->getStoreId())) {
                return false;
            }
        } catch (\Throwable $t) {
            $this->logger->error(
                sprintf(
                    self::LOG_PREFIX . $t->getMessage()
                )
            );
        }
        return $proceed($order, $index, $customer, $captureCase);
    }

}
