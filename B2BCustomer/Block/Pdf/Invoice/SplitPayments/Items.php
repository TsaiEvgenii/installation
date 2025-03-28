<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Pdf\Invoice\SplitPayments;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\IsB2BSplitService;
use Magento\Framework\View\Element\Template;
use BelVG\BusinessCreditPayment\Model\BusinessCredit;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;
use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;

class Items extends \BelVG\QuotePdf\Block\View\Order\Items
{

    /**
     * @var \BelVG\QuotePdf\API\GetQuoteInterface
     */
    protected $getQuoteService;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var IsB2BSplitService
     */
    protected $b2bSplitService;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Customer\API\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var PartAmountResolver
     */
    protected $partAmountResolver;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var PartialInvoiceService
     */
    protected $partialInvoiceService;


    /**
     * @var
     */
    protected $index;

    /**
     * @var
     */
    protected $customer;


    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param IsB2BSplitService $b2bSplitService
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param PartAmountResolver $partAmountResolver
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Config $config
     * @param PartialInvoiceService $partialInvoiceService
     * @param \BelVG\QuotePdf\API\GetQuoteInterface $getQuoteService
     * @param array $data
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory|null $itemCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context                $context,
        \Magento\Framework\Registry                                     $registry,
        \Magento\Sales\Api\InvoiceRepositoryInterface                   $invoiceRepository,
        IsB2BSplitService                                               $b2bSplitService,
        \Magento\Customer\Api\CustomerRepositoryInterface               $customerRepository,
        PartAmountResolver                                              $partAmountResolver,
        \Magento\Framework\App\RequestInterface                         $request,
        \BelVG\B2BCustomer\Model\Config                                 $config,
        PartialInvoiceService                                           $partialInvoiceService,
        \BelVG\QuotePdf\API\GetQuoteInterface                           $getQuoteService,
        array                                                           $data = [],
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory = null
    )
    {

        $this->b2bSplitService = $b2bSplitService;
        $this->customerRepository = $customerRepository;
        $this->partialInvoiceService = $partialInvoiceService;
        $this->partAmountResolver = $partAmountResolver;
        $this->invoiceRepository = $invoiceRepository;
        $this->request = $request;
        $this->config = $config;
        $this->getQuoteService = $getQuoteService;
        parent::__construct($context, $registry, $getQuoteService, $data, $itemCollectionFactory);
    }


    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->getQuoteService->get();
    }

    /**
     * @return \Magento\Framework\DataObject|\Magento\Sales\Api\Data\InvoiceInterface
     */
    public function getInvoice()
    {
        if ($invoiceId = $this->request->getParam('invoice_id')) {
            return $this->invoiceRepository->get($invoiceId);
        } else {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->getQuoteService->get();
            /** @var \Magento\Sales\Model\Order\Invoice $lastInvoice */
            return $order->getInvoiceCollection()->getLastItem();
        }
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        $order = $this->getOrder();
        if ($this->getInvoice()->getIncrementId()) {
            $incrementArr = explode('-', $this->getInvoice()->getIncrementId());
            $index = array_pop($incrementArr);
            if (
                $this->b2bSplitService->isAllowed($order->getCustomerGroupId(), (int)$order->getStoreId())
                && $this->partialInvoiceService->isAllowed($order, $index)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getInvoiceText()
    {
        return $this->config->getInvoiceText();
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        if (!$this->index) {
            $currentInvoice = $this->getInvoice();
            $invoices = $this->getOrder()->getInvoiceCollection();
            $i = 1;
            foreach ($invoices as $index => $invoice) {
                if ($invoice->getEntityId() === $currentInvoice->getEntityId()) {
                    $this->index = $i;
                    break;
                }
                $i++;
            }
        }
        return $this->index;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->customerRepository->getById($this->getOrder()->getCustomerId());
        }
        return $this->customer;
    }

    /**
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPartRate($index = null)
    {
        if ($index === null) {
            $index = $this->getIndex();
        }
        return $this->partAmountResolver->getPaymentRate($this->getCustomer(), $index);
    }

    /**
     * @return int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPartAmount($index = null)
    {
        if ($index === null) {
            $index = $this->getIndex();
        }
        return $this->partAmountResolver->getPaymentAmountFormatted($this->getCustomer(), $index, $this->getOrder());
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentDate()
    {
        $delay = $this->getCustomer()->getCustomAttribute(PartialInvoiceService::B2B_SPLIT_PERIOD . ($this->getIndex() - 1));
        if ($delay && $delay->getValue()) {
            $date = new \DateTime($this->getInvoice()->getCreatedAt());
            $date->modify('+' . $delay->getValue() . ' day');
            return $date->format('d/m-Y');
        }
        return false;
    }

    public function getTaxRatePercentage(): string
    {
        $order = $this->getOrder();
        return $this->partAmountResolver->getTaxRatePercentage($order) . '%';
    }

    public function getTaxAmount($index = null){
        if ($index === null) {
            $index = $this->getIndex();
        }

        return $this->partAmountResolver->getTaxAmount($this->getCustomer(), $index, $this->getOrder());
    }
    public function getOrderGrandTotal(): string
    {
        $order = $this->getOrder();
        $customer = $this->getCustomer();

        return $this->partAmountResolver->getOrderGrandTotal($order, $customer);
    }
    public function getOrderTax(): string
    {
        $order = $this->getOrder();
        $customer = $this->getCustomer();

        return $this->partAmountResolver->getOrderTax($order, $customer);
    }

}
