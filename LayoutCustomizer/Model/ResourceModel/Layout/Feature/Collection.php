<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Feature;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature as FeatureResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Param\CollectionFactory
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
        $this->_init(Feature::class, FeatureResource::class);
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
        $featureIds = $this->getAllIds();
        if (!empty($featureIds)) {
            $collection = $this->paramCollectionFactory
                ->create()
                ->addFeatureFilter($featureIds);
            foreach ($collection as $param) {
                $feature = $this->getItemById($param->getFeatureId());
                // assert($feature);
                $feature->setParam($param->getName(), $param->getValue());
            }
        }
        return $this;
    }
}
