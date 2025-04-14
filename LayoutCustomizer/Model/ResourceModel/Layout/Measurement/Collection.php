<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Measurement;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement as MeasurementResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\Param\CollectionFactory
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
        $this->_init(Measurement::class, MeasurementResource::class);
    }

    public function addBlockFilter($blockIds)
    {
        return $this->addFieldToFilter('block_id', $blockIds);
    }

    protected function _afterLoad()
    {
        $this->loadParams();
        return parent::_afterLoad();
    }

    protected function loadParams()
    {
        $measurementIds = $this->getAllIds();
        if (!empty($measurementIds)) {
            $collection = $this->paramCollectionFactory
                ->create()
                ->addMeasurementFilter($measurementIds);
            foreach ($collection as $param) {
                $measurement = $this->getItemById($param->getMeasurementId());
                // assert($measurement);
                $measurement->setParam($param->getName(), $param->getValue());
            }
        }
        return $this;
    }
}
