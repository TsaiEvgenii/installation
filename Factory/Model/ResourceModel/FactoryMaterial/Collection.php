<?php
namespace BelVG\Factory\Model\ResourceModel\FactoryMaterial;

use BelVG\Factory\Model\FactoryMaterial;
use BelVG\Factory\Model\ResourceModel\FactoryMaterial as FactoryMaterialResource;
use BelVG\Factory\Model\ResourceModel\DeliveryRule\CollectionFactory as DeliveryRuleCollectionFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    use \BelVG\Factory\Model\DefaultStoreId;

    protected $deliveryRuleCollectionFactory;

    public function __construct(
        DeliveryRuleCollectionFactory $deliveryRuleCollectionFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource);
        $this->deliveryRuleCollectionFactory = $deliveryRuleCollectionFactory;

        $this->addFilterToMap('factory_material_id', 'main_table.factory_material_id');
    }

    protected function _construct()
    {
        $this->_init(FactoryMaterial::class, FactoryMaterialResource::class);
    }

    public function addFactoryFilter($factoryId)
    {
        return $this->addFieldToFilter('factory_id', $factoryId);
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->assignDeliveryRules();
        foreach ($this->getItems() as $item) {
            $item->setStoreId($this->getStoreId());
        }
        return $this;
    }

    protected function assignDeliveryRules()
    {
        $factoryMaterialIds = $this->walk('getId');
        // [factory_material_id => [rule1, rule2, ...]]
        $byFactoryMaterialId = [];

        if (!empty($factoryMaterialIds)) {
            $collection = $this->deliveryRuleCollectionFactory
                ->create()
                ->addFactoryMaterialFilter($factoryMaterialIds)
                ->addStoreFilter($this->getStoreId());
            foreach ($collection as $deliveryRule) {
                $factoryMaterialId = $deliveryRule->getFactoryMaterialId();
                isset($byFactoryMaterialId[$factoryMaterialId])
                    or $byFactoryMaterialId[$factoryMaterialId] = [];

                $byFactoryMaterialId[$factoryMaterialId][] = $deliveryRule->getData();
            }
        }

        foreach ($this->getItems() as $item) {
            $factoryMaterialId = $item->getFactoryMaterialId();
            $deliveryRules = ($byFactoryMaterialId[$factoryMaterialId] ?? []);
            $item->setDeliveryRules($deliveryRules);
        }
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->joinStoreData();
//print_r([
//    $this->getStoreId(),
//    (string)$this->getSelect()
//]); //die;
        return $this;
    }

    protected function getStoreIds() :array {
        $storeIds = [0]; //by default (as fallback)

        $storeId = $this->getStoreId();
        if ($storeId != 0) {
            $storeIds[] = (int)$storeId;
        }

        return $storeIds;
    }

    protected function joinStoreData()
    {
        $select = $this->getSelect();

        $select->where('store_id IN (?)', $this->getStoreIds());
    }
}
