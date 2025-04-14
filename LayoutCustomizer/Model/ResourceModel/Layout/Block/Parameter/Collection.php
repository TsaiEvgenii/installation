<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\Parameter;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter as ParameterResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter\Option\CollectionFactory
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

    public function addBlockFilter($blockId)
    {
        return $this->addFieldToFilter('block_id', $blockId);
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
