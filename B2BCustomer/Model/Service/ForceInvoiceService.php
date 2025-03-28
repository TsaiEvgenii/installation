<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
declare(strict_types=1);


namespace BelVG\B2BCustomer\Model\Service;



use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\InvoiceManagementInterface;
use BelVG\ShippingManagerActions\Model\Service\InvoiceServiceInterface;
use Magento\Sales\Model\Order\Invoice;

class ForceInvoiceService implements InvoiceServiceInterface
{
    /**
     * @var InvoiceManagementInterface
     */
    private $invoiceManagement;
    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * InvoiceService constructor.
     * @param InvoiceManagementInterface $invoiceManagement
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(InvoiceManagementInterface $invoiceManagement,
                                TransactionFactory         $transactionFactory)
    {
        $this->invoiceManagement = $invoiceManagement;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param $order
     * @param $captureCase
     * @return void
     * @throws LocalizedException
     */
    public function execute($order, $captureCase = null): void
    {
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
            'Created by force invoice service'
        );

        if ($captureCase === null) {
            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
        } else {
            $invoice->setRequestedCaptureCase($captureCase);
        }

        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);
        $this->saveInvoice($invoice);
    }

    /**
     * @param Invoice $invoice
     */
    private function saveInvoice(Invoice $invoice)
    {
        $transactionSave = $this->transactionFactory->create()
            ->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            )->save();
        return $invoice;
    }
}
