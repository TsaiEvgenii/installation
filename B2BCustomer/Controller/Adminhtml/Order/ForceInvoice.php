<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Controller\Adminhtml\Order;

use BelVG\B2BCustomer\Model\Service\ForceInvoiceService;
use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Backend\App\Action\Context;

class ForceInvoice extends Action
{

    protected $invoiceService;

    protected $orderRepository;

    public function __construct(
        ForceInvoiceService $invoiceService,
        OrderRepositoryInterface $orderRepository,
        Context             $context
    )
    {
        $this->invoiceService = $invoiceService;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            $this->invoiceService->execute($order);
            $this->messageManager->addSuccessMessage(__('Invoice was successfully created'));
        } catch (\Throwable $t) {
            $this->messageManager->addExceptionMessage(
                $t,
                __('Something went wrong while saving the invoice')
            );
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
