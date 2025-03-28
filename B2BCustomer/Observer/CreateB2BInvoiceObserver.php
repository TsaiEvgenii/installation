<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Observer;

use BelVG\B2BCustomer\Model\Service\CheckB2BEmailSent;
use BelVG\B2BCustomer\Model\Service\InvoiceService;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;
use BelVG\OrderEdit\Api\Data\OrderEditable;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use BelVG\B2BCustomer\Model\Config;
use Magento\Sales\Model\Order\ItemRepository;
use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;
use BelVG\B2BCustomer\Model\Service\InvoiceEmailSender;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CreateB2BInvoiceObserver implements ObserverInterface
{
    /**
     *
     */
    private const LOG_PREFIX = '[BelVG_B2BCustomer::CreateB2BInvoiceObserver]: ';
    const B2B_SPLIT_PAYMENT_DATA = 'b2b_split_payment_data';


    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var InvoiceService
     */
    private InvoiceService $invoiceService;
    /**
     * @var CustomerCheck
     */
    private CustomerCheck $customerCheck;
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ItemRepository
     */
    private ItemRepository $itemRepository;

    /**
     * @var IsB2BSplitService
     */
    private IsB2BSplitService $b2BSplitService;

    /**
     * @var PartialInvoiceService
     */
    private PartialInvoiceService $partialInvoiceService;

    /**
     * @var InvoiceEmailSender
     */
    private InvoiceEmailSender $invoiceEmailSender;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    private PartAmountResolver $partAmountResolver;



    /**
     * @param CustomerCheck $customerCheck
     * @param Config $config
     * @param ItemRepository $itemRepository
     * @param InvoiceService $invoiceService
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param InvoiceEmailSender $invoiceEmailSender
     * @param PartialInvoiceService $partialInvoiceService
     * @param IsB2BSplitService $b2BSplitService
     */
    public function __construct(
        CustomerCheck               $customerCheck,
        Config                      $config,
        ItemRepository              $itemRepository,
        InvoiceService              $invoiceService,
        LoggerInterface             $logger,
        PartAmountResolver         $partAmountResolver,
        CustomerRepositoryInterface $customerRepository,
        InvoiceEmailSender          $invoiceEmailSender,
        PartialInvoiceService       $partialInvoiceService,
        IsB2BSplitService           $b2BSplitService
    )
    {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->invoiceEmailSender = $invoiceEmailSender;
        $this->itemRepository = $itemRepository;
        $this->customerCheck = $customerCheck;
        $this->invoiceService = $invoiceService;
        $this->partialInvoiceService = $partialInvoiceService;
        $this->partAmountResolver = $partAmountResolver;
        $this->b2BSplitService = $b2BSplitService;
        $this->logger = $logger;
    }

    /**
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var Order $order */
            $order = $observer->getEvent()->getOrder();
            if ($order->getData(OrderEditable::FIELD) == OrderEditable::ORDER_FIXED) {
                if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId())) {
                    $invoicesCount = $order->getInvoiceCollection()->count();
                    $index = $invoicesCount + 1;
                    if ($this->partialInvoiceService->isAllowed($order, $index)) {
                        $customer = $this->customerRepository->getById($order->getCustomerId());
                        if (!$invoicesCount) {
                            $this->invoiceService->execute($order, $index, $customer);
                            $this->sendInvoiceEmail($order, $customer);
                        } elseif ($order->getStatus() === $this->config->getPaymentStatus($invoicesCount, (int)$order->getStoreId())) {
                            $this->invoiceService->execute($order, $index, $customer);
                            $this->sendInvoiceEmail($order, $customer);
                            if ($index === Config::PAYMENTS_COUNT || ($index + 1 === Config::PAYMENTS_COUNT && $this->partAmountResolver->getPaymentAmount($customer, $index, $order) === 0)) {
                                $this->invoiceService->execute($order, $index, $customer, null, true);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $t) {
            $this->logger->error(self::LOG_PREFIX . $t->getMessage());
        }
    }

    /**
     * @param $order
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function sendInvoiceEmail($order, $customer)
    {
        $this->invoiceEmailSender->sendEmail($order, $customer);
        $paymentsData = [];
        if ($customer->getCustomAttribute(self::B2B_SPLIT_PAYMENT_DATA)) {
            $paymentsData = json_decode($customer->getCustomAttribute(self::B2B_SPLIT_PAYMENT_DATA)->getValue(), true);
        }
        $paymentsData[$order->getEntityId()][$order->getInvoiceCollection()->count()] = true;
        $customer->setCustomAttribute(self::B2B_SPLIT_PAYMENT_DATA, json_encode($paymentsData));
        $customer->setData('ignore_validation_flag', true);
        $this->customerRepository->save($customer);
        $order->getPayment()->setAdditionalInformation(CheckB2BEmailSent::B2B_EMAIL_SENT . $order->getInvoiceCollection()->count(), true);
    }
}
