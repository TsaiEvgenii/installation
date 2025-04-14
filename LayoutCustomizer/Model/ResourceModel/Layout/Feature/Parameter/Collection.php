<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Feature\Parameter;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter as ParameterResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter\Option\CollectionFactory
    as OptionCollectionFactory;

class Collection extends AbstractCollection
{
    protected $optionCollectionFactory;

    public function __construct(
        OptionCollectionFactory $optionCollectionFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->optionCollectionFactory = $optionCollectionFactory;
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
        $this->_init(Parameter::class, ParameterResource::class);
    }

    public function addFeatureFilter($featureId)
    {
        return $this->addFieldToFilter('feature_id', $featureId);
    }

    protected function _afterLoad()
    {
        $this->loadOptions();
        return parent::_afterLoad();
    }

    protected function loadOptions()
    {
        $parameterIds = $this->getAllIds();
        if (!empty($parameterIds)) {
            $collection = $this->optionCollectionFactory
                ->create()
                ->addParameterFilter($parameterIds);
            foreach ($collection as $option) {
                $parameter = $this->getItemById($option->getParameterId());
                // assert($parameter);
                $parameter->addOption($option);
            }
        }
    }
}
