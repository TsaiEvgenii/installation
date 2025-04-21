<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class AddMeasurementToolElementsToQuote
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::AddMeasurementToolElementsToQuoteService]: ';

    public function __construct(
        protected MeasurementToolRepositoryInterface $measurementToolRepository,
        protected ResourceConnection $resourceConnection,
        protected Session $customerSession,
        protected LoggerInterface $logger
    ) {
    }

    public function add($measurementToolId)
    {
        try {
            $customerId = $this->customerSession->getCustomerId();
            $elementsData = [];
            $measurementTool = $this->measurementToolRepository->getById($measurementToolId);
            $rooms = $measurementTool->getRooms();
            foreach ($rooms as $room){
                $elements = $room->getElements();
                foreach ($elements as $element){
                    $elementsData[] = [
                        'customer_id'         => $customerId,
                        'measurement_tool_id' => $measurementToolId,
                        'room_id'             => $room->getEntityId(),
                        'room_name'           => $room->getName(),
                        'element_id'          => $element->getEntityId(),
                        'type'                => $element->getType(),
                        'name'                => $element->getName(),
                        'width'               => $element->getWidth(),
                        'height'              => $element->getHeight(),
                        'qty'                 => $element->getQty()
                    ];
                }
            }
            if(count($elementsData) > 0){
                $connection = $this->resourceConnection->getConnection();
                $tableName = $connection->getTableName('belvg_measurement_tool_customer_element');
                $result = $connection->insertMultiple($tableName, $elementsData);

                return $result;
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