<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Service\InstallationProductHandler\HandlersPool;
use Magento\Sales\Api\Data\OrderInterface;

class InstallationProductHandler
{
    public function __construct(
        protected HandlersPool $handlersPool
    ){}

    public function handle(OrderInterface $order): void
    {
        foreach ($this->handlersPool->getHandlers() as $handler) {
            if ($handler->isAvailable($order)) {
                $handler->execute($order);
            }
        }
        unset($handler);
    }
}