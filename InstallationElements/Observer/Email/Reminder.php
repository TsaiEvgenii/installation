<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Observer\Email;


use BelVG\InstallationElements\Model\InstallationReminderFactory;
use BelVG\InstallationElements\Model\InstallationReminder;
use BelVG\InstallationElements\Model\ResourceModel\InstallationReminder as InstallationReminderResourceModel;
use BelVG\InstallationElements\Model\Service\CheckOrderInstallation;
use BelVG\InstallationElements\Model\Service\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Reminder implements ObserverInterface
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::EmailReminderObserver]: ';

    public function __construct(

        protected Config $installationElementsConfig,
        protected InstallationReminderFactory $installationReminderModelFactory,
        protected InstallationReminderResourceModel $installationReminderResource,
        protected CheckOrderInstallation $checkOrderInstallationService,
        protected LoggerInterface $logger

    ) {
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var  \Magento\Sales\Model\Order\Status\History $statusHistory */
            $statusHistory = $observer->getEvent()->getData('status_history');
            $currentStatus = $statusHistory->getStatus();
            $orderId = $statusHistory->getParentId();
            $reminderStatuses = explode(',', $this->installationElementsConfig->getReminderStatus());
            if (
                $this->checkOrderInstallationService->orderIncludeInstallationProduct($orderId)
                && in_array($currentStatus, $reminderStatuses)
            ) {
                $delayOfDays = $this->installationElementsConfig->getReminderDelay();
                $dateToSend = new \DateTime();
                $dateToSend->modify('+' . $delayOfDays . ' day');
                /** @var InstallationReminder $reminderModel */
                $reminderModel = $this->installationReminderModelFactory->create();
                $reminderModel->setData('order_id', $orderId);
                $reminderModel->setData('should_sent_after', $dateToSend);
                $this->installationReminderResource->save($reminderModel);
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