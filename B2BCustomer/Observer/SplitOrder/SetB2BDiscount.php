<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Observer\SplitOrder;


use BelVG\B2BCustomer\Model\Total\Quote\B2BDiscountTotals;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Tax\Model\Calculation;
use Psr\Log\LoggerInterface;

class SetB2BDiscount implements ObserverInterface
{
    public function __construct(
        protected Calculation $calculation,
        protected LoggerInterface $logger
    ) {
    }

    private const LOG_PREFIX = '[BelVG_B2BCustomer::SetB2BDiscountObserver]: ';

    public function execute(Observer $observer)
    {
        try {
            /** @var Order $parentOrder */
            $parentOrder = $observer->getData('parentOrder');
            /** @var Order $order */
            $order = $observer->getData('order');
            $parentB2bDiscount = $parentOrder->getData(B2BDiscountTotals::B2B_DISCOUNT_KEY);
            if ($parentB2bDiscount) {
                $childParentRatio = $order->getData('subtotal_incl_tax') / $parentOrder->getData('subtotal_incl_tax');
                $childB2bDiscount = $parentB2bDiscount * $childParentRatio;

                $order->setData(B2BDiscountTotals::B2B_DISCOUNT_KEY, $childB2bDiscount);
                $order->setData('base_grand_total', $order->getData('base_grand_total') - $childB2bDiscount);
                $order->setData('grand_total', $order->getData('grand_total') - $childB2bDiscount);

                $b2bDiscountTax = $this->getB2bDiscountTax($parentB2bDiscount, $order);
                $childB2bDiscountTax = $b2bDiscountTax * $childParentRatio;
                $taxAmount = $order->getTaxAmount();
                $baseTaxAmount = $order->getBaseTaxAmount();
                $order->setTaxAmount($taxAmount - (float)$childB2bDiscountTax);
                $order->setBaseTaxAmount($baseTaxAmount - (float)$childB2bDiscountTax);
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

    }

    protected function getB2bDiscountTax($b2bDiscount, $order): float
    {
        $taxRate = round($order->getSubtotalInclTax() * 100 / $order->getSubtotal() - 100);

        return $this->calculation->calcTaxAmount($b2bDiscount, $taxRate, true, false);
    }
}