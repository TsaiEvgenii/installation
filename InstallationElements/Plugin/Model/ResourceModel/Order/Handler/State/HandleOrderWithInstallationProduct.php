<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Plugin\Model\ResourceModel\Order\Handler\State;


use BelVG\InstallationElements\Model\Service\CheckOrderInstallation;
use BelVG\InstallationElements\Model\Service\Config;
use BelVG\InstallationElements\Model\Service\InstallationProductHandler;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateManager;
use Psr\Log\LoggerInterface;

class HandleOrderWithInstallationProduct
{

    private const LOG_PREFIX = '[BelVG_InstallationElements::HandleOrderWithInstallationProductPlugin]: ';

    public function __construct(
        protected Config $installationElementsConfig,
        protected CheckOrderInstallation $checkOrderInstallationService,
        protected InstallationProductHandler $installationProductHandler,
        protected LoggerInterface $logger
    ) {
    }

    public function afterCheck(
        StateManager $subject,
        StateManager $result,
        Order $order
    ): StateManager {
        try {
            if(!$this->checkOrderInstallationService->orderIncludeInstallationProduct($order->getId())){
                return $result;
            }

            $newStatus = $order->getData(OrderInterface::STATUS);
            $oldStatus = $order->getOrigData(OrderInterface::STATUS);
            $isChangedStatus = $oldStatus !== $newStatus;
            if (!$isChangedStatus) {
                return $result;
            }

            $allowedStatuses = explode(
                ',',
                $this->installationElementsConfig->getRouteplannerSettings((int)$order->getStoreId())['create_ticket_status'] ?? []
            );
            if (!in_array($newStatus, $allowedStatuses)) {
                return $result;
            }

            $this->installationProductHandler->handle($order);

        } catch (\Throwable $t) {
            $this->logger->error(
                sprintf(
                    self::LOG_PREFIX . ' something went wrong: %s',
                    $t->getMessage()
                ),
                $t->getTrace()
            );
        }

        return $result;
    }
}