<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout;

use BelVG\LayoutCustomizer\Helper\Layout\StoreData as StoreDataHelper;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $storeManager;
    protected $storeDataHelper;
    protected $storeId;

    protected $_idFieldName = 'layout_id';

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StoreDataHelper $storeDataHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->storeManager = $storeManager;
        $this->storeDataHelper = $storeDataHelper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource);

        $this->addFilterToMap('layout_id', 'main_table.layout_id');
        $this->addFilterToMap('identifier', 'main_table.identifier');
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \BelVG\LayoutCustomizer\Model\Layout::class,
            \BelVG\LayoutCustomizer\Model\ResourceModel\Layout::class
        );
    }

    public function addFieldToSelect($field, $alias = null)
    {
        if (!in_array($field, $this->storeDataHelper->getFields())) {
            parent::addFieldToSelect($field, $alias);
        }
        return $this;
    }

    public function addIsEmptyFilter()
    {
        $this->getSelect()
            ->joinLeft(
                ['blocks' => $this->getTable('belvg_layoutcustomizer_layout_block')],
                'blocks.layout_id = main_table.layout_id AND blocks.parent_id IS NULL',
                ['root_block_num' => new \Zend_Db_Expr('COUNT(blocks.block_id)')])
            ->group('main_table.layout_id')
            ->having('root_block_num = 0');
        return $this;
    }

    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->setStoreId($this->storeManager->getStore()->getId());
        }
        return $this->storeId;
    }

    public function setStoreId($store)
    {
        $this->storeId = $this->storeManager->getStore($store)->getId();
        return $this;
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->joinStoreData();
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
        $storeId = $this->getStoreId();
        $this->storeDataHelper->join($this, $storeId);
    }
}
