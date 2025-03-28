<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\Model\Order;

use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Sales\Model\Order;
use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;
class Invoice
{

    /**
     * @var IsB2BSplitService
     */
    protected IsB2BSplitService $b2BSplitService;

    protected PartialInvoiceService $partialInvoiceService;


    /**
     * @param IsB2BSplitService $b2BSplitService
     */
    public function __construct(
        IsB2BSplitService $b2BSplitService,
        PartialInvoiceService $partialInvoiceService
    )
    {
        $this->partialInvoiceService = $partialInvoiceService;
        $this->b2BSplitService = $b2BSplitService;
    }

    /**
     * @param $subject
     * @param $result
     * @param $id
     * @return mixed
     */
    public function afterSetIncrementId($subject, $result, $id)
    {
        if ($this->b2BSplitService->isAllowed($subject->getOrder()->getCustomerGroupId(), $subject->getOrder()->getStoreId())) {
            if ($this->partialInvoiceService->isAllowed($subject->getOrder(), $subject->getOrder()->getInvoiceCollection()->count())) {
                $result->setData('increment_id', $id . '-B2B-' . $subject->getOrder()->getInvoiceCollection()->count());
            }
        }
        return $result;
    }


}
