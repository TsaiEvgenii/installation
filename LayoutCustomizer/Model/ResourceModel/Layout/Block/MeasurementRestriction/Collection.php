<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\MeasurementRestriction;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction
    as MeasurementRestrictionResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction\Param\CollectionFactory
    as ParamCollectionFactory;

class Collection extends AbstractCollection
{
    protected $paramCollectionFactory;

    public function __construct(
        ParamCollectionFactory $paramCollectionFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->paramCollectionFactory = $paramCollectionFactory;
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
        $this->_init(MeasurementRestriction::class, MeasurementRestrictionResource::class);
    }

    public function addBlockFilter($blockId)
    {
        return $this->addFieldToFilter('block_id', $blockId);
    }

    protected function _afterLoad()
    {
        $this->loadParams();
        return parent::_afterLoad();
    }

    protected function loadParams()
    {
        $restrictionIds = $this->getAllIds();
        if (!empty($restrictionIds)) {
            $collection = $this->paramCollectionFactory
                ->create()
                ->addMeasurementRestrictionFilter($restrictionIds);
            foreach ($collection as $param) {
                $restriction = $this->getItemById($param->getMeasurementRestrictionId());
                // assert($restriction);
                $restriction->setParam($param->getName(), $param->getValue());
            }
        }
        return $this;
    }
}
