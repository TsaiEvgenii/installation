<?php
namespace BelVG\Factory\Model\ResourceModel\Factory;

use BelVG\Factory\Helper\Factory\StoreData as StoreDataHelper;
use BelVG\Factory\Model\Factory;
use BelVG\Factory\Model\ResourceModel\Factory as FactoryResource;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    use \BelVG\Factory\Model\DefaultStoreId;

    protected $storeDataHelper;

    protected $storeDataFilters = [];

    public function __construct(
        StoreDataHelper $storeDataHelper,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        Event\ManagerInterface $eventManager,
        AdapterInterface $connection = null)
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            null);
        $this->storeDataHelper = $storeDataHelper;

        $this->_idFieldName = 'factory_id';
        $this->addFilterToMap('factory_id', 'main_table.factory_id');
    }

    protected function _construct()
    {
        $this->_init(Factory::class, FactoryResource::class);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, $this->storeDataHelper->getFields())) {
            $this->addStoreDataFieldFilter($field, $condition);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->joinStoreData();
        $this->applyStoreDataFilters();
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->getItems() as $item)
            $item->setStoreId($this->getStoreId());
        return $this;
    }

    protected function joinStoreData()
    {
        $this->storeDataHelper->join($this);
    }

    protected function addStoreDataFieldFilter($field, $condition = null)
    {
        $this->storeDataFilters[] = [$field, $condition];
    }

    protected function applyStoreDataFilters()
    {
        foreach ($this->storeDataFilters as $filter) {
            list($field, $condition) = $filter;
            $fieldExpr = $this->storeDataHelper->getFieldExpr($field, $this->getStoreId());
            $where = $condition
                ? $this->_getConditionSql($fieldExpr, $condition)
                : $field;
            $this->getSelect()->where($where);
        }
    }

    /**
     * Set bulk change factory filter
     *
     * @param array $factoryIds
     *
     * @return $this
     */
    public function setBulkChangeFactory($factoryIds)
    {
        return $this->addFieldToFilter('factory_id', ['in' => $factoryIds]);
    }
}
