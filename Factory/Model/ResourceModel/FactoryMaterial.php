<?php
namespace BelVG\Factory\Model\ResourceModel;

use BelVG\Factory\Model\DeliveryRuleFactory;
use BelVG\Factory\Model\ResourceModel\DeliveryRule\CollectionFactory
    as DeliveryRuleCollectionFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db;

class FactoryMaterial extends Db\AbstractDb
{
    protected $deliveryRuleFactory;
    protected $deliveryRuleCollectionFactory;

    public function __construct(
        DeliveryRuleFactory $deliveryRuleFactory,
        DeliveryRuleCollectionFactory $deliveryRuleCollectionFactory,
        Db\Context $context,
        $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->deliveryRuleFactory = $deliveryRuleFactory;
        $this->deliveryRuleCollectionFactory = $deliveryRuleCollectionFactory;
    }

    protected function _construct()
    {
        $this->_init('belvg_factory_material', 'factory_material_id');
    }

    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->loadDeliveryRules($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->saveDeliveryRules($object);
    }

    protected function loadDeliveryRules(AbstractModel $object)
    {
        $collection = $this->getDeliveryRuleCollection($object);
        $deliveryRulesData = $collection->walk('getData');
        $object->setDeliveryRules($deliveryRulesData);
    }

    protected function getDeliveryRuleCollection(AbstractModel $object)
    {
        return $this->deliveryRuleCollectionFactory
            ->create()
            ->addFactoryMaterialFilter($object->getId())
            ->addStoreFilter($object->getStoreId())
            ->addOrder('sort_order');
    }

    protected function saveDeliveryRules($object)
    {
        // Get old delivery rules
        $oldDeliveryRuleCollection = $this->getDeliveryRuleCollection($object);
        $oldDeliveryRuleIdMap = array_fill_keys(
            $oldDeliveryRuleCollection->walk('getId'), true);

        // Save
        $items = $object->getDeliveryRules() ?: [];
        foreach ($items as $item) {
            // Get or create model
            $deliveryRuleId = $item['delivery_rule_id'] ?? null;
            $deliveryRule = null;
            if ($deliveryRuleId) {
                $deliveryRule = $oldDeliveryRuleCollection->getItemById($deliveryRuleId);
                if (!$deliveryRule) continue;
                unset($oldDeliveryRuleIdMap[$deliveryRuleId]);

            } else {
                $deliveryRule = $this->deliveryRuleFactory->create();
            }

            // Update data (but not ID)
            unset($item['delivery_rule_id']);
            $item['category_id'] = ($item['category_id'] ?? null);
            $deliveryRule
                ->addData($item)
                ->setFactoryMaterialId($object->getId())
                ->setStoreId($object->getStoreId());

            // Save
            $deliveryRule->save();
        }

        // Delete
        $idsToDelete = array_keys($oldDeliveryRuleIdMap);
        foreach ($idsToDelete as $deliveryRuleId) {
            $deliveryRule = $oldDeliveryRuleCollection->getItemById($deliveryRuleId);
            if ($deliveryRule) {
                $deliveryRule->delete();
            }
        }
    }
}
