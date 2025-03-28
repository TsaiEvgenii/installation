<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Total\OrderInvoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use BelVG\B2BCustomer\Model\Service\DiscountService;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;

/**
 * Class Discount
 *
 * @package BelVG\B2BCustomer\Model\Total\OrderInvoice
 */
class Discount extends AbstractTotal
{
    /**
     * @var DiscountService
     */
    protected DiscountService $discountService;

    /**
     * Discount constructor.
     *
     * @param DiscountService $discountService
     * @param string[] $data
     */
    public function __construct(
        DiscountService $discountService,
        array $data = []
    ) {
        parent::__construct($data);
        $this->discountService = $discountService;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return $this
     */
    public function collect(InvoiceInterface $invoice): self
    {
        parent::collect($invoice);

        if ($order = $invoice->getOrder()) {
            $this->discountService->subtructDiscount($order, $invoice);
        }

        return $this;
    }


}
