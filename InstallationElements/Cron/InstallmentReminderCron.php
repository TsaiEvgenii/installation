<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Cron;


use BelVG\EventNotifier\Model\SenderContext;
use BelVG\InstallationElements\Model\ResourceModel\InstallationReminder\Collection as InstallationReminderCollection;
use BelVG\InstallationElements\Model\ResourceModel\InstallationReminder\CollectionFactory;
use BelVG\InstallationElements\Model\ResourceModel\InstallationReminder as InstallationReminderResource;
use BelVG\InstallationElements\Model\InstallationReminder;
use BelVG\InstallationElements\Model\Service\Config;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class InstallmentReminderCron
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::InstallmentReminderCron]: ';

    public function __construct(
        protected CollectionFactory $installationReminderCollectionFactory,
        protected InstallationReminderResource $installationReminderResource,
        protected SenderContext $emailSender,
        protected TransportBuilder $transportBuilder,
        protected OrderRepositoryInterface $orderRepository,
        protected Config $installationElementsConfig,
        protected LoggerInterface $logger
    ) {
    }

    public function execute()
    {
        try {
            /** @var InstallationReminderCollection $installationReminderCollection */
            $installationReminderCollection = $this->installationReminderCollectionFactory->create();
            $currentData = new \DateTime();
            $installationReminderCollection->addFieldToFilter('is_processed', ['neq' => '1']);
            $installationReminderCollection->addFieldToFilter('should_sent_after', ['lteq' => $currentData]);
            if ($installationReminderCollection->count() > 0) {
                /** @var InstallationReminder $installationReminder */
                foreach ($installationReminderCollection as $installationReminder) {
                    //Send email
                    $orderId = $installationReminder->getData('order_id');
                    $order = $this->orderRepository->get($orderId);
                    $storeId = (int)$order->getStoreId();
                    $internalSubscribers = explode(
                        ',',
                        $this->installationElementsConfig->getReminderSubscribers($storeId) ?? ''
                    );
                    $this->transportBuilder
                        ->setTemplateIdentifier($this->installationElementsConfig->getReminderTemplate($storeId))
                        ->setTemplateOptions([
                            'area'  => Area::AREA_FRONTEND,
                            'store' => $storeId
                        ])
                        ->setTemplateVars([
                            'order'      => $order,
                            'store'      => $order->getStore(),
                            'order_data' => [
                                'customer_name'         => $order->getCustomerName(),
                                'email_customer_note'   => $order->getEmailCustomerNote(),
                                'frontend_status_label' => $order->getFrontendStatusLabel(),
                            ]
                        ])
                        ->setFromByScope('general')
                        ->addTo($order->getCustomerEmail())
                        ->addBcc($internalSubscribers);
                    $conditionsFilePath = $this->installationElementsConfig->getConditionFilePath($storeId);
                    if ($conditionsFilePath !== '') {
                        $fileName = basename($conditionsFilePath);
                        $this->transportBuilder->addAttachment(
                            file_get_contents($conditionsFilePath),
                            $fileName,
                            'application/pdf'
                        );
                    }
                    $transport = $this->transportBuilder->getTransport();

                    $transport->sendMessage();
                    $installationReminder->setData('is_processed', 1);
                    $this->installationReminderResource->save($installationReminder);
                }
            }

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
    }
}