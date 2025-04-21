<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Plugin\Checkout\Controller\Index;


use Magento\Checkout\Controller\Index\Index as Subject;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

class CleanMeasurementToolElements
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::CleanMeasurementToolElementsPlugin]: ';
    public function __construct(
        protected Session $customerSession,
        protected ResourceConnection $resourceConnection,
        protected LoggerInterface $logger
    ) {

    }

    public function afterExecute(
        Subject $subject,
        ResultInterface $result
    ): ResultInterface {
        try {
            if (!$this->customerSession->isLoggedIn()) {
                return $result;
            }

            $connection = $this->resourceConnection->getConnection();
            $tableName = $connection->getTableName('belvg_measurement_tool_customer_element');

            $connection->delete(
                $tableName,
                ['customer_id = ?' => $this->customerSession->getCustomerId()]);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $result;
    }
}