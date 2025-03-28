<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\AdditionalServices\Model\Service\GetSubtotalWithoutServices;
use Laminas\Validator\ValidatorInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Api\Data\OrderInterface;
use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\SalesRule\Model\Validator\Pool;
use Magento\Tax\Model\Calculation;

class DiscountService
{

    protected $calculation;

    public function __construct(
        protected Pool $salesRuleValidatorPool,
        protected GetSubtotalWithoutServices $getSubtotalWithoutServices,
        Calculation $calculation,

    )
    {
        $this->calculation = $calculation;
    }

    public function getDiscountAmount($quote, $discount)
    {
        if (!$quote || $quote->getItems() === null) {
            return 0;
        }
        $subtotal = $this->getSubtotalWithoutServices->getSubtotalInclTaxFromQuote($quote);

        return $subtotal - ($subtotal * (100 - $discount) / 100);
    }

    public function canApplyDiscount(AbstractItem $item)
    {
        $result = true;
        /** @var ValidatorInterface $validator */
        foreach ($this->salesRuleValidatorPool->getValidators('b2b_discount') as $validator) {
            $result = $validator->isValid($item);
            if (!$result) {
                break;
            }
        }
        return $result;
    }

    public function getTaxAmount($entity, $newAmount)
    {
        $taxRate = round($entity->getSubtotalInclTax() * 100 / $entity->getSubtotal() - 100);
        return $this->calculation->calcTaxAmount($newAmount, $taxRate, true);
    }

    public function subtructDiscount(OrderInterface $order, $entity): void
    {
        $discount = (float)$order->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY);
        if ($discount) {
            if ($entity instanceof \Magento\Sales\Api\Data\InvoiceInterface) {
                $discount = $this->getDiscountWithoutTax($discount, $entity);
            }
            $entity->setGrandTotal($entity->getGrandTotal() - $discount);
            $entity->setBaseGrandTotal($entity->getBaseGrandTotal() - $discount);
        }
    }

    protected function getDiscountWithoutTax($discount, \Magento\Sales\Api\Data\InvoiceInterface $invoice)
    {
        if ($invoice->getSubtotal()) {
            $taxRate = round($invoice->getSubtotalInclTax() * 100 / $invoice->getSubtotal() - 100);
            $taxAmount = $this->calculation->calcTaxAmount($discount, $taxRate, true, false);
            return $discount - $taxAmount;
        }
        return 0;
    }
}
