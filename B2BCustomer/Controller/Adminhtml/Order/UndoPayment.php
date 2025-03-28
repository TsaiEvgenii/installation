<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Controller\Adminhtml\Order;

use BelVG\B2BCustomer\Model\Config;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;

class UndoPayment extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk::home';


    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected OrderPaymentRepositoryInterface $orderPaymentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected InvoiceRepositoryInterface $invoiceRepository;


    public function __construct(
        Context                         $context,
        OrderRepositoryInterface        $orderRepository,
        InvoiceRepositoryInterface      $invoiceRepository,
        OrderPaymentRepositoryInterface $orderPaymentRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $paymentId = $data['payment_id'];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = ['status' => true];
        try {
            $payment = $this->orderPaymentRepository->get((int)$paymentId);
            if ($payment && isset($data['index'])) {
                $additionalInfo = $payment->getAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY);
                if ($additionalInfo) {
                    $additionalInfo = json_decode($additionalInfo, true);
                    if (isset($additionalInfo[$data['index']])) {
                        unset($additionalInfo[$data['index']]);
                    }
                    $payment->setAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY, json_encode($additionalInfo));
                    $this->orderPaymentRepository->save($payment);
                    $order = $this->orderRepository->get($payment->getParentId());
                    $invoices = $order->getInvoiceCollection();
                    if ($invoices) {
                        $invoices = $invoices->toArray();
                        if (isset($invoices['items'][$data['index'] - 1])) {
                            $invoice = $this->invoiceRepository->get($invoices['items'][$data['index'] - 1]['entity_id']);
                            $invoice->setState(Invoice::STATE_OPEN);
                            $this->invoiceRepository->save($invoice);
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $result['status'] = false;
            $result['message'] = $exception->getMessage();
        }

        $resultJson->setData($result);
        return $resultJson;
    }
}
