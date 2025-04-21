<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model\Service;


use BelVG\MeasurementTool\Api\CustomerElementRepositoryInterface;
use BelVG\MeasurementTool\Api\Data\CustomerElementInterface;
use BelVG\MeasurementTool\Api\Webapi\CustomerElementsManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class CustomerElementsManager implements CustomerElementsManagerInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::CustomerElementsManagerService]: ';
    public function __construct(
        protected ResourceConnection $resourceConnection,
        protected CustomerElementRepositoryInterface $customerElementRepository,
        protected FilterBuilder $filterBuilder,
        protected FilterGroupBuilder $filterGroupBuilder,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected LoggerInterface $logger
    ) {

    }

    public function getCustomerElements($customerId): array
    {
        try {
            $filterCustomerId = $this->filterBuilder
                ->setField(CustomerElementInterface::CUSTOMER_ID)
                ->setValue($customerId)
                ->setConditionType('eq')
                ->create();

            $filterGroupCustomerId = $this->filterGroupBuilder->addFilter($filterCustomerId)->create();
            $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupCustomerId])->create();
            $customerElementsResult = $this->customerElementRepository->getList($criteria);

            return $customerElementsResult->getItems();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return [];
    }

    /**
     * @param int $customerId
     * @param int $elementId
     *
     * @return bool
     */
    public function removeCustomerElement(int $customerId, int $elementId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('belvg_measurement_tool_customer_element');
        $sql = $connection->select()
            ->from($tableName)
            ->where('customer_id = ?', $customerId)
            ->where('entity_id = ?', $elementId);
        $result = $connection->query($sql->deleteFromSelect($tableName));

        return (bool)$result;
    }
}