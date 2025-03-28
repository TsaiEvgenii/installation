<?php

namespace BelVG\B2BCustomer\Plugin\Model\Service;

use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Sales\Model\Order;

class SendC1BEmail
{

    /**
     * @var IsB2BSplitService
     */
    protected IsB2BSplitService $b2BSplitService;


    /**
     * @param IsB2BSplitService $b2BSplitService
     */
    public function __construct(
        IsB2BSplitService $b2BSplitService,

    )
    {
        $this->b2BSplitService = $b2BSplitService;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param Order $order
     * @param $notify
     * @return false
     */
    public function aroundSend($subject, callable $proceed, Order $order, $notify = true)
    {
        if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId())) {
            return false;
        }
        return $proceed($order, $notify);
    }

}
