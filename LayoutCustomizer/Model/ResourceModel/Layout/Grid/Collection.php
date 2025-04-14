<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use BelVG\LayoutCustomizer\Helper\Layout\StoreData as StoreDataHelper;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Collection as LayoutCollection;

class Collection extends LayoutCollection implements SearchResultInterface
{
    protected $aggregations;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StoreDataHelper $storeDataHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        parent::__construct(
            $storeManager,
            $storeDataHelper,
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource);
        $this->_init(
            $model,
            \BelVG\LayoutCustomizer\Model\ResourceModel\Layout::class);

        $this->getSelect()->joinLeft(
            ['materials' => $this->getResource()->getTable('belvg_layoutmaterial_layoutmaterial')],
            'materials.layoutmaterial_id = main_table.layoutmaterial_id',
            ['materials.name as material']
        );

        $this->addFilterToMap('material', 'materials.name');
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

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
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
