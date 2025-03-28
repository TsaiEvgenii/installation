<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Plugin\ViewModel\Order;


use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Elasticsearch\Endpoints\Get;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\RequestInterface;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;
use BelVG\QuotePdf\API\GetQuoteInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class CaptureInfo
{

    private IsB2BSplitService $b2BSplitService;

    private RequestInterface $request;

    private GetQuoteInterface $getQuoteService;
    private PartAmountResolver $partAmountResolver;

    private CustomerRepository $customerRepository;

    private InvoiceRepositoryInterface $invoiceRepository;

    private PricingHelper $pricingHelper;

    public function __construct(
        RequestInterface           $request,
        CustomerRepository         $customerRepository,
        GetQuoteInterface          $getQuoteService,
        InvoiceRepositoryInterface $invoiceRepository,
        PartAmountResolver         $partAmountResolver,
        PricingHelper              $pricingHelper,
        IsB2BSplitService          $b2BSplitService
    )
    {
        $this->request = $request;
        $this->getQuoteService = $getQuoteService;
        $this->pricingHelper = $pricingHelper;
        $this->invoiceRepository = $invoiceRepository;
        $this->customerRepository = $customerRepository;
        $this->partAmountResolver = $partAmountResolver;
        $this->b2BSplitService = $b2BSplitService;
    }

    public function getOrder()
    {
        return $this->getQuoteService->get();
    }


    public function getInvoice()
    {
        if ($invoiceId = $this->request->getParam('invoice_id')) {
            return $this->invoiceRepository->get($invoiceId);
        } else {
            $order = $this->getQuoteService->get();
            return $order->getInvoiceCollection()->getLastItem();
        }
    }

    public function afterGetAmountPaid($subject, $result)
    {
        $order = $this->getOrder();
        if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId())) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            $currentInvoice = $this->getInvoice();
            $invoices = $this->getOrder()->getInvoiceCollection();
            $index = 1;
            foreach ($invoices as $invoice) {
                if ($invoice->getEntityId() === $currentInvoice->getEntityId()) {
                    break;
                }
                $index++;
            }
            $result = $this->partAmountResolver->getAmountPaid($customer, $index, $order);
            return $this->pricingHelper->currencyByStore($result, $order->getStoreId());
        }
        return $result;
    }
}
