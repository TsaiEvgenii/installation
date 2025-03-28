<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Pdf\Html;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Framework\View\Element\Template;
use BelVG\BusinessCreditPayment\Model\BusinessCredit;

class Header extends \BelVG\QuotePdf\Block\Html\Header\Logo
{

    /**
     * @var \BelVG\QuotePdf\API\GetQuoteInterface
     */
    protected $getQuoteService;

    /**
     * @var
     */
    protected $config;

    /**
     * @var \BelVG\B2BCustomer\Model\Service\IsB2BSplitService
     */
    protected $b2bSplitService;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var
     */
    protected $index;

    /**
     * @param Template\Context $context
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \BelVG\QuotePdf\API\GetQuoteInterface $getQuoteService
     * @param \BelVG\B2BCustomer\Model\Service\IsB2BSplitService $b2bSplitService
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context   $context,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        \Magento\Sales\Api\InvoiceRepositoryInterface      $invoiceRepository,
        \BelVG\QuotePdf\API\GetQuoteInterface              $getQuoteService,
        \BelVG\B2BCustomer\Model\Service\IsB2BSplitService $b2bSplitService,
        \Magento\Framework\App\RequestInterface            $request,
        array                                              $data = []
    )
    {
        $this->b2bSplitService = $b2bSplitService;
        $this->invoiceRepository = $invoiceRepository;
        $this->request = $request;
        $this->getQuoteService = $getQuoteService;
        parent::__construct($context, $fileStorageHelper, $data);
    }


    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->getQuoteService->get();
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        $order = $this->getOrder();
        if (
            $this->b2bSplitService->isAllowed($order->getCustomerGroupId(), (int)$order->getStoreId())
            && $this->getIndex() <= Config::PAYMENTS_COUNT
        ) {
            return true;
        }
        return false;
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

}
