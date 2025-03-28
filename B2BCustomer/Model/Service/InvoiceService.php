<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Model\Service;


use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;
use BelVG\ShippingManagerActions\Model\Service\InvoiceServiceInterface;
use Magento\Sales\Model\Order\Invoice;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;

class InvoiceService implements InvoiceServiceInterface
{

    /**
     * @var InvoiceManagementInterface
     */
    private InvoiceManagementInterface $invoiceManagement;

    /**
     * @var TransactionFactory
     */
    private TransactionFactory $transactionFactory;

    /**
     * @var \BelVG\B2BCustomer\Model\Service\PartAmountResolver
     */
    private PartAmountResolver $partAmountResolver;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;


    /**
     * @param InvoiceManagementInterface $invoiceManagement
     * @param \BelVG\B2BCustomer\Model\Service\PartAmountResolver $partAmountResolver
     * @param CustomerRepository $customerRepository
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        InvoiceManagementInterface $invoiceManagement,
        PartAmountResolver         $partAmountResolver,
        CustomerRepository         $customerRepository,
        TransactionFactory         $transactionFactory
    )
    {
        $this->invoiceManagement = $invoiceManagement;
        $this->customerRepository = $customerRepository;
        $this->partAmountResolver = $partAmountResolver;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param Order $order
     * @param $index
     * @param $customer
     * @param $captureCase
     * @return void
     * @throws LocalizedException
     */
    public function execute(Order $order, $index = null, $customer = null, $captureCase = null, $lastInvoice = false): void
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceManagement->prepareInvoice($order);
            if (!$invoice) {
                throw new LocalizedException(__('We can\'t save the invoice right now.'));
            }
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $invoice->addComment(
                'Created by invoice service'
            );

            if ($captureCase === null) {
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
            } else {
                $invoice->setRequestedCaptureCase($captureCase);
            }
            if (!$lastInvoice) {
                $invoice->setState(Invoice::STATE_OPEN);
                $amount = $this->partAmountResolver->getPaymentAmount($customer, $index, $order);
                $invoice->setGrandTotal($amount);
                $invoice->setSubtotal($amount);
            } else {
                $invoice->register();
                $invoice->getOrder()->setIsInProcess(true);
            }
            $this->saveInvoice($invoice, $lastInvoice);
        };
    }

    /**
     * @param Invoice $invoice
     */
    private function saveInvoice(Invoice $invoice, $lastInvoice)
    {
        $transactionSave = $this->transactionFactory->create()
            ->addObject(
                $invoice
            );
        if ($lastInvoice) {
            $transactionSave->addObject(
                $invoice->getOrder()
            );
        }
        $transactionSave->save();
        return $invoice;
    }
}
