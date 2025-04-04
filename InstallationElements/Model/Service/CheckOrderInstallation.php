<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service;


use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Framework\App\ResourceConnection;

class CheckOrderInstallation
{
    public function __construct(
        protected ResourceConnection $resource,
        protected InstallationProductConfig $installationProductConfig
    )
    {
    }

    public function orderIncludeInstallationProduct($orderId): bool
    {
        $connection = $this->resource->getConnection();
        $salesOrderItemTable = $connection->getTableName('sales_order_item');
        $select = $connection->select()
            ->from($salesOrderItemTable)
            ->where('order_id = ?', $orderId)
            ->where('product_type = ?', $this->installationProductConfig->getProductType());
        $result = $connection->fetchOne($select);

        return (bool)$result;
    }
}