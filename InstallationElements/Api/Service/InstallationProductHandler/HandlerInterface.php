<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\InstallationElements\Api\Service\InstallationProductHandler;

use Magento\Sales\Api\Data\OrderInterface;

interface HandlerInterface
{
    public function isAvailable(OrderInterface $order): bool;

    public function execute(
        OrderInterface $order
    );
}