<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\AutoProcessOrderService;


use BelVG\AutoProcessOrder\Api\AutoProcessOrderBlockerInterface;
use BelVG\InstallationElements\Model\Service\CheckOrderInstallation;
use Magento\Sales\Api\Data\OrderInterface;

class InstallmentOrderBlocker implements AutoProcessOrderBlockerInterface
{
    public function __construct(
        protected CheckOrderInstallation $checkOrderInstallationService

    ) {
    }

    public function isBlocked(OrderInterface $order): bool
    {
        return $this->checkOrderInstallationService->orderIncludeInstallationProduct($order->getEntityId());
    }
}