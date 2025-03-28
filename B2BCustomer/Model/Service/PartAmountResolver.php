<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\B2BCustomer\Model\Config;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Tax\Model\Calculation;

class PartAmountResolver
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var array
     */
    protected $rates = [];
    /**
     * @var
     */
    protected $amountToPay;

    /**
     * @param Config $config
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerCheck $customerCheck
     */
    public function __construct(
        Config                 $config,
        PriceCurrencyInterface $priceCurrency,
        CustomerCheck          $customerCheck,
        protected Calculation  $taxCalculation,
    )
    {
        $this->priceCurrency = $priceCurrency;
        $this->customerCheck = $customerCheck;
        $this->config = $config;
    }

    /**
     * @param $customer
     * @param $index
     * @return null
     */
    public function getPaymentRate($customer, $index)
    {
        $attribute = $customer->getCustomAttribute(PartialInvoiceService::B2B_SPLIT_PAYMENT . $index);
        return $attribute?->getValue();
    }

    /**
     * @param $customer
     * @param $index
     * @param $amount
     * @return int|string
     */
    public function getPaymentAmount($customer, $index, $order)
    {
        $i = 1;
        $this->amountToPay = $order->getGrandTotal();
        if (!isset($this->rates[$order->getEntityId()])) {
            while($this->getPaymentRate($customer, $i)) {
                $rate = $this->getPaymentRate($customer, $i);
                $this->rates[$order->getEntityId()][$i] = $this->priceCurrency->roundPrice($order->getGrandTotal() * ((int)$rate / 100), 0);
                $this->amountToPay -= $this->rates[$order->getEntityId()][$i];
                $i++;
            }
            if (isset($this->rates[$order->getEntityId()][$i - 1])){
                $this->rates[$order->getEntityId()][$i - 1] =   $this->priceCurrency->roundPrice($this->rates[$order->getEntityId()][$i - 1] + $this->amountToPay, 0);
            }
        }
        if (isset($this->rates[$order->getEntityId()][$index])) {
            return $this->rates[$order->getEntityId()][$index];
        }
        return 0;
    }

    /**
     * @param $customer
     * @param $index
     * @param $amount
     * @return string
     */
    public function getPaymentAmountFormatted($customer, $index, $order)
    {
        return $this->priceCurrency->format($this->getPaymentAmount($customer, $index, $order), false, PriceCurrencyInterface::DEFAULT_PRECISION, $customer->getStoreId());
    }

    /**
     * @param $customer
     * @param $index
     * @param $order
     * @return int|mixed
     */
    public function getAmountPaid($customer, $index, $order)
    {
        $result = 0;
        $this->getPaymentAmount($customer, $index, $order);
        $paymentData = $order->getPayment()->getAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY);
        if ($paymentData) {
            $paymentData = json_decode($paymentData, true);
        } else {
            $paymentData = [];
        }
        if (isset($this->rates[$order->getEntityId()])){
            foreach ($this->rates[$order->getEntityId()] as $index => $rate) {
                if (isset($paymentData[$index])) {
                    $result += $rate;
                }
            }
        }

        return $result;
    }

    public function getTaxAmount($customer, $index, $order){
        $amount = $this->getPaymentAmount($customer, $index, $order);
        $taxRate = $this->getTaxRatePercentage($order);
        return $this->priceCurrency->format($this->taxCalculation->calcTaxAmount($amount, $taxRate, true, false), false, PriceCurrencyInterface::DEFAULT_PRECISION, $customer->getStoreId());

    }
    public function getTaxRate(OrderInterface $order): float
    {
        return $this->priceCurrency->roundPrice($order->getSubtotalInclTax() / $order->getSubtotal() - 1);
    }
    public function getTaxRatePercentage(OrderInterface $order): float|int
    {
        return $this->getTaxRate($order) * 100;
    }

    public function getOrderGrandTotal(OrderInterface $order, $customer): string
    {
        return $this->priceCurrency->format(
            $order->getGrandTotal(),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $customer->getStoreId()
        );
    }

    public function getOrderTax(OrderInterface $order, $customer): string
    {
        return $this->priceCurrency->format(
            $order->getTaxAmount(),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $customer->getStoreId()
        );
    }
}
