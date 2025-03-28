<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Cron;

use BelVG\B2BCustomer\Model\Service\PartialInvoiceService;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder as MailTransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use BelVG\B2BCustomer\Model\Config;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use BelVG\B2BCustomer\Model\Service\PartAmountResolver;

class PaymentReport
{


    const EMAIL_TEMPLATE_ID = 'b2b_split_email_report';

    const EMAIL_SENDER = 'general';

    const DK_STORE_CODE = 'default';

    const SECOND_INVOICE = 1;

    const THIRD_INVOICE = 2;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MailTransportBuilder
     */
    protected $mailTransportBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepostory;

    /**
     * @var PartAmountResolver
     */
    protected $partAmountResolver;


    /**
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param MailTransportBuilder $mailTransportBuilder
     * @param PartAmountResolver $partAmountResolver
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param Config $config
     * @param DateTime $dateTime
     */
    public function __construct(
        LoggerInterface             $logger,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface       $storeManager,
        MailTransportBuilder        $mailTransportBuilder,
        PartAmountResolver          $partAmountResolver,
        OrderCollectionFactory      $orderCollectionFactory,
        StoreRepositoryInterface    $storeRepository,
        Config                      $config,
        DateTime                    $dateTime
    )
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager = $storeManager;
        $this->customerRepostory = $customerRepository;
        $this->partAmountResolver = $partAmountResolver;
        $this->storeRepository = $storeRepository;
        $this->mailTransportBuilder = $mailTransportBuilder;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $b2bGroups = $this->config->getAllowedCustomerGroups();
        if ($b2bGroups) {
            $reportData = [];
            $orderCollection = $this->orderCollectionFactory->create();
            $orderCollection->addFieldToFilter('customer_group_id', explode(',', $b2bGroups));
            $orderCollection->addFieldToFilter('state', ['nin' => Order::STATE_CANCELED]);
            $orderCollection->addFieldToFilter('state', ['nin' => Order::STATE_CLOSED]);
            foreach ($orderCollection->getItems() as $order) {
                if ($this->config->getIsSplitEnabled($order->getStoreId())) {
                    $invoices = $order->getInvoiceCollection()->toArray();
                    $customer = $this->customerRepostory->getById($order->getCustomerId());
                    if (isset($invoices['items'][self::SECOND_INVOICE])) {
                        $this->setDataByIndex($reportData, self::SECOND_INVOICE, $invoices['items'][self::SECOND_INVOICE], $order, $customer);
                    }
                    if (isset($invoices['items'][self::THIRD_INVOICE])) {
                        $this->setDataByIndex($reportData, self::THIRD_INVOICE, $invoices['items'][self::THIRD_INVOICE], $order, $customer);
                    }
                }
            }
            $emailSubject = 'B2B orders report';
            $emailData = [
                'report' => [
                    'items' => $reportData,
                    'status_' . self::SECOND_INVOICE => str_replace('_', ' ', ucfirst($this->config->getPaymentStatus(self::SECOND_INVOICE))),
                    'status_' . self::THIRD_INVOICE => str_replace('_', ' ', ucfirst($this->config->getPaymentStatus(self::THIRD_INVOICE))),
                    'invoice_index_' . self::SECOND_INVOICE => self::SECOND_INVOICE,
                    'invoice_index_' . self::THIRD_INVOICE => self::THIRD_INVOICE,
                    'payments_count' => Config::PAYMENTS_COUNT
                ]
            ];

            if ($this->config->getB2BSplitEmailAddresses() && $reportData) {
                $addressesTo = explode(',', $this->config->getB2BSplitEmailAddresses());
                try {
                    $store = $this->storeRepository->get(self::DK_STORE_CODE);
                } catch (NoSuchEntityException $e) {
                    $store = $this->storeManager->getStore();
                }
                foreach ($addressesTo as $addressTo) {
                    // Send
                    $this->mailTransportBuilder
                        ->setTemplateIdentifier(self::EMAIL_TEMPLATE_ID)
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store->getId(),
                        ])
                        ->addTo($addressTo)
                        ->setFromByScope(self::EMAIL_SENDER)
                        ->setTemplateVars(
                            array_merge(
                                $emailData,
                                [
                                    'subject' => $emailSubject,
                                    'store' => $store,
                                ]));
                    /** @var \Magento\Framework\Mail\TransportInterface $transport */
                    $transport = $this->mailTransportBuilder->getTransport();

                    try {
                        $transport->sendMessage();
                    } catch (MailException $e) {
                        $this->logger->critical($e);
                    }
                }
                unset($addressTo);
            }

        }
    }

    /**
     * @param $reportData
     * @param $index
     * @param $invoice
     * @param $order
     * @param $customer
     * @return void
     * @throws \Exception
     */
    public function setDataByIndex(&$reportData, $index, $invoice, $order, $customer)
    {
        $period = $customer->getCustomAttribute(PartialInvoiceService::B2B_SPLIT_PERIOD . self::SECOND_INVOICE)?->getValue();
        if ($period) {
            $nowDate = new \DateTime();
            $deadLineDate = new \DateTime($invoice['created_at']);
            $deadLineDate->modify('+' . $period . ' days');
            $paymentAdditionalInfo = [];
            if ($order->getPayment()->getAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY)) {
                $paymentAdditionalInfo = json_decode($order->getPayment()->getAdditionalInformation(Config::B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY), true);
            }
            if ($deadLineDate < $nowDate && !isset($paymentAdditionalInfo[$index])) {
                $reportData[$index][$order->getEntityId()]['exceeded_time'] = $deadLineDate->format('d.m.Y (h:i)');
                $reportData[$index][$order->getEntityId()]['increment_id'] = $order->getIncrementId();
                $reportData[$index][$order->getEntityId()]['amount'] = $this->partAmountResolver->getPaymentAmountFormatted($customer, $index + 1, $order);
            }
        }
    }
}
