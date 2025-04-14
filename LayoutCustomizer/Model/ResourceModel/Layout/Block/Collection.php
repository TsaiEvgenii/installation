<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block as BlockResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\ShapeParam\CollectionFactory
    as ShapeParamCollectionFactory;


class Collection extends AbstractCollection
{
    protected $shapeParamCollectionFactory;

    public function __construct(
        ShapeParamCollectionFactory $shapeParamCollectionFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->shapeParamCollectionFactory = $shapeParamCollectionFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource);
    }

    protected function _construct()
    {
        $this->_init(Block::class, BlockResource::class);
    }

    public function addLayoutFilter($layoutId)
    {
        return $this->addFieldToFilter('layout_id', $layoutId);
    }

    public function addNonEmptyNameFilter()
    {
        return $this
            ->addFieldToFilter('name', ['neq' => ''])
            ->addFieldToFilter('name', ['notnull' => true]);
    }

    public function addIsRootFilter($isRoot)
    {
        $condition = [($isRoot ? 'null' : 'notnull') => true];
        return $this->addFieldToFilter('parent_id', $condition);
    }

    public function joinLayoutIdentifiers()
    {
        $select = $this->getSelect();
        $from = $select->getPart(\Zend_Db_Select::FROM);
        if (!isset($from['layout_identifier'])) {
            $select->join(
                ['layout_identifier' => $this->getTable('belvg_layoutcustomizer_layout')],
                'layout_identifier.layout_id = main_table.layout_id',
                ['layout_identifier' => 'layout_identifier.identifier']);
        }
        return $this;
    }

    protected function _afterLoad()
    {
        $this->loadShapeParams();
        return parent::_afterLoad();
    }

    protected function loadShapeParams()
    {
        $blockIds = $this->getAllIds();
        if (!empty($blockIds)) {
            $collection = $this->shapeParamCollectionFactory
                ->create()
                ->addBlockFilter($blockIds);
            foreach ($collection as $param) {
                $block = $this->getItemById($param->getBlockId());
                // assert($block);
                $block->setShapeParam($param->getName(), $param->getValue());
            }
        }
        return $this;
    }
}
