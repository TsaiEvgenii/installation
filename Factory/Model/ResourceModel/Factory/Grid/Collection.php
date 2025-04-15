<?php
namespace BelVG\Factory\Model\ResourceModel\Factory\Grid;

use BelVG\Factory\Helper\Factory\StoreData as StoreDataHelper;
use BelVG\Factory\Model\ResourceModel\Factory as FactoryResource;
use BelVG\Factory\Model\ResourceModel\Factory\Collection as FactoryCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event;
use Magento\Framework\View\Element\UiComponent\DataProvider;
use Psr\Log\LoggerInterface;

class Collection extends FactoryCollection implements SearchResultInterface
{
    protected \Magento\Framework\Api\Search\AggregationInterface $aggregations;

    public function __construct(
        StoreDataHelper $storeDataHelper,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        Event\ManagerInterface $eventManager,
        $modelClass = DataProvider\Document::class,
        AdapterInterface $connection = null)
    {
        parent::__construct(
            $storeDataHelper,
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection);
        $this->_init($modelClass, FactoryResource::class);
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setItems(array $items = null)
    {
        return $this;
    }
}
