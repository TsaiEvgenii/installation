<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\Model\Order;

use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Sales\Model\Order;

class CanInvoice
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


    public function afterCanInvoice(Order $order, bool $result) : bool
    {
        if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId()) && $order->getState() == Order::STATE_COMPLETE) {
            $result = true;
        }
        return $result;
    }

}
