<?php

namespace BelVG\B2BCustomer\ViewModel\Order;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;
use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;

class Info implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const ORDER_CREATED_LABEL = 'Order created';
    protected CustomerSession $customerSession;

    protected CustomerRepositoryInterface $customerRepository;

    protected Config $config;
    protected PartAmountResolver $partAmountResolver;

    protected PriceCurrencyInterface $priceCurrency;

    protected CustomerCheck $customerCheck;


    protected $totalRate;
    protected $currentCustomer;

    public function __construct(
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Config $config,
        PartAmountResolver $partAmountResolver,
        PriceCurrencyInterface $priceCurrency,
        CustomerCheck $customerCheck
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->config = $config;
        $this->partAmountResolver = $partAmountResolver;
        $this->priceCurrency = $priceCurrency;
        $this->customerCheck = $customerCheck;
    }

    protected function getCustomer($order)
    {
        if (!$this->currentCustomer) {
            try {
                $customerId = $order->getCustomerId();
                if ($customerId) {
                    $this->currentCustomer = $this->customerRepository->getById($customerId);
                } else {
                    return false;
                }
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return $this->currentCustomer;
    }

    public function isEnabled($order)
    {
        if ($this->getCustomer($order)) {
            $paymentMethod = $order->getPayment()->getMethod();
            $allowedPaymentMethods = explode(',', $this->config->getPaymentMethods() ?? '');
            if (
                $allowedPaymentMethods
                && $this->customerCheck->isB2BCustomer($this->getCustomer($order)->getGroupId(), $this->getCustomer($order)->getStoreId())
                && $this->config->getIsSplitEnabled((int)($order->getStoreId()))
                && array_search($paymentMethod, $allowedPaymentMethods) !== false
            ) {
                return true;
            }
        }
        return false;
    }

    public function getStatus(int $index)
    {
        $status = $this->config->getPaymentStatus($index);
        if (!$status) {
            return self::ORDER_CREATED_LABEL;
        }
        return str_replace('_', ' ', ucfirst($status));
    }

    public function getPaymentRate(int $index, $order)
    {
        $attribute = $this->getCustomer($order)->getCustomAttribute(PartialInvoiceService::B2B_SPLIT_PAYMENT . $index);
        $this->totalRate += (int)$attribute?->getValue();
        return (int)$attribute?->getValue();
    }

    public function getPaymentAmount(int $index, $order)
    {
        return $this->partAmountResolver->getPaymentAmountFormatted($this->getCustomer($order), $index, $order);
    }

    public function getPartIsPaid($index, $order)
    {
        $paymentInfo = $order->getPayment()->getAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY);
        if ($paymentInfo) {
            $paymentInfo = json_decode($paymentInfo, true);
            if (isset($paymentInfo[$index])) {
                return $paymentInfo[$index];
            }
        }
        return false;
    }

    public function formatPaidDate(string $date)
    {
        return $date ? implode(' - ', explode(':', $date, 2)) : null;
    }

    public function getGrandTotal($order)
    {
        return $this->priceCurrency->format($order->getGrandTotal(), true, PriceCurrencyInterface::DEFAULT_PRECISION, $order->getStoreId());
    }

    public function getTotalRate()
    {
        return $this->totalRate;
    }

    public function getPaymentsCount()
    {
        return Config::PAYMENTS_COUNT;
    }
}
