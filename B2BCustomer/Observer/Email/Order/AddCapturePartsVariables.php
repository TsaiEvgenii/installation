<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Observer\Email\Order;

use BelVG\B2BCustomer\Model\Service\PartAmountResolver;
use BelVG\PartialCapture\Api\Service\HelperServiceInterface as PartialCaptureHelperService;
use BelVG\QuotePdf\API\GetSecretInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url;

class AddCapturePartsVariables implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var IsB2BSplitService
     */
    protected IsB2BSplitService $b2BSplitService;

    /**
     * @var PartialCaptureHelperService
     */
    protected $partialCaptureHelper;

    /**
     * @var PartAmountResolver
     */
    protected $partAmountResolver;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var
     */
    protected $urlBuilder;
    /**
     * @var Url
     */
    private $url;
    /**
     * @var GetSecretInterface
     */
    private $secure;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param Url $url
     * @param PartAmountResolver $partAmountResolver
     * @param GetSecretInterface $secure
     * @param PartialCaptureHelperService $partialCaptureHelper
     * @param IsB2BSplitService $b2BSplitService
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Url                                               $url,
        PartAmountResolver                                $partAmountResolver,
        GetSecretInterface                                $secure,
        PartialCaptureHelperService                       $partialCaptureHelper,
        IsB2BSplitService $b2BSplitService
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->partAmountResolver = $partAmountResolver;
        $this->partialCaptureHelper = $partialCaptureHelper;
        $this->b2BSplitService = $b2BSplitService;
        $this->url = $url;
        $this->secure = $secure;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $transport = $observer->getEvent()->getData('transportObject');
        if (is_object($transport)) {
            $order = $transport->getOrder();
            if ($this->b2BSplitService->isAllowed($order->getCustomerGroupId(), $order->getStoreId())) {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                $amount = $this->partAmountResolver->getPaymentAmountFormatted($customer, $order->getInvoiceCollection()->count(), $order);
                $percent = $this->partAmountResolver->getPaymentRate($customer, $order->getInvoiceCollection()->count());
                $invoiceLink = $this->url->getUrl(
                    'quotepdf/pdf/invoice',
                    [
                        'order' => $order->getId(),
                        'invoice_id' => $order->getInvoiceCollection()->getLastItem()->getEntityId(),
                        'secret' => $this->secure->get($order->getId())
                    ]);
                $transport->setData('bankwire_part1_capture', $amount);
                $transport->setData('first_payment', $amount);
                $transport->setData('first_payment_part_percent', $percent);
                $transport->setData('bankwire_first_payment_part_percent', $percent);
                $transport->setData('invoice_pdf_link', $invoiceLink);
                $transport->setData('is_split_b2b', true);
                $this->partialCaptureHelper->replacePaymentHtmlVariables($transport, [
                    'bankwire_part1_capture' => $amount,
                    'first_payment' => $amount,
                    'first_payment_part_percent' => $percent,
                    'bankwire_first_payment_part_percent' => $percent,
                    'invoice_pdf_link' => $invoiceLink
                ]);
            }
        }

    }
}
