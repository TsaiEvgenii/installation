<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Pdf;

use BelVG\B2BCustomer\Model\Config;
use BelVG\B2BCustomer\Model\Service\CustomerCheck;
use Magento\Framework\View\Element\Template;
use BelVG\BusinessCreditPayment\Model\BusinessCredit;
use BelVG\QuotePdf\API\GetQuoteInterface;

class B2BArea extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \BelVG\QuotePdf\API\GetQuoteInterface
     */
    protected $getQuoteService;

    /**
     * @var CustomerCheck
     */
    protected $customerCheck;

    /**
     * @var Config
     */
    protected $config;


    /**
     * @param Template\Context $context
     * @param CustomerCheck $customerCheck
     * @param Config $config
     * @param GetQuoteInterface $getQuoteService
     * @param array $data
     */
    public function __construct(
        Template\Context  $context,
        CustomerCheck     $customerCheck,
        Config            $config,
        GetQuoteInterface $getQuoteService,
        array             $data = []
    )
    {
        $this->config = $config;
        $this->customerCheck = $customerCheck;
        $this->getQuoteService = $getQuoteService;
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
    public function isAreaAllowed()
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
}
