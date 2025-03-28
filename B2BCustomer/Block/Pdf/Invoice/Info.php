<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Pdf\Invoice;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Framework\View\Element\Template;
use BelVG\BusinessCreditPayment\Model\BusinessCredit;

class Info extends \Magento\Framework\View\Element\Template
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
     * @var CustomerCheck
     */
    protected $customerCheck;


    /**
     * @param Template\Context $context
     * @param CustomerCheck $customerCheck
     * @param Config $config
     * @param \BelVG\QuotePdf\API\GetQuoteInterface $getQuoteService
     * @param array $data
     */
    public function __construct(
        Template\Context                      $context,
        CustomerCheck                         $customerCheck,
        \BelVG\B2BCustomer\Model\Config       $config,
        \BelVG\QuotePdf\API\GetQuoteInterface $getQuoteService,
        array                                 $data = []
    )
    {
        $this->getQuoteService = $getQuoteService;
        $this->customerCheck = $customerCheck;
        $this->config = $config;
        parent::__construct($context, $data);
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
        $payment = $order->getPayment();
        if (
            ($payment->getMethod() === BusinessCredit::PAYMENT_METHOD_CODE
            || $this->customerCheck->isB2BCustomer($order->getCustomerGroupId(), $order->getStoreId()))
            && !$this->config->getIsSplitEnabled($order->getStoreId())
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return \Magento\Framework\Phrase|void
     */
    public function getText()
    {
        $paymentDeadline = $this->config->getPaymentDeadline();
        if ($paymentDeadline) {
            return __('Payment deadline: %1 days', $paymentDeadline);
        }
    }
}
