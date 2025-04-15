<?php
namespace BelVG\Factory\Ui\DataProvider\Factory\Form\Modifier;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Helper\Factory\StoreData as StoreDataHelper;
use BelVG\Factory\Model\FactoryFactory as ModelFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class ObjectData extends AbstractModifier
{
    protected $storeManager;
    protected $dataPersistor;
    protected $modelFactory;
    protected $storeDataHelper;
    protected $dataConverter;

    protected $objectData;

    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        ModelFactory $modelFactory,
        StoreDataHelper $storeDataHelper,
        ExtensibleDataObjectConverter $dataConverter)
    {
        parent::__construct($registry);
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
        $this->storeDataHelper = $storeDataHelper;
        $this->dataConverter = $dataConverter;
    }

    public function modifyData(array $data)
    {
        $model = $this->getObject();
        $data[$model->getFactoryId() ?: 0] = $this->getObjectData();
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $objectData = $this->getObjectData();
        if ($this->getStoreId() != 0) {
            foreach ($this->storeDataHelper->getFields() as $field) {

                $this->addUseDefaultCheckbox($meta, $objectData, $field);
            }
            unset($field);

            foreach ($this->storeDataHelper->getCustomFields() as $field) {
                $this->addUseDefaultCheckbox($meta, $objectData, $field);
            }
        }
        return $meta;
    }

    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    protected function getObjectData()
    {
        if (is_null($this->objectData)) {
            // Get current object
            $object = $this->getObject();

            // Get store data
            $storeData = [];
            if ($object->getFactoryId()) {
                $storeData = $this->storeDataHelper
                    ->loadById(
                        $object->getFactoryId(),
                        $this->getStoreId(),
                        false /* do not load default */);
            }

            // Merge store data, persisted data, store ID
            $this->objectData = array_merge(
                $storeData,
                $this->dataConverter->toNestedArray($object, [], FactoryInterface::class),
                (array) $this->dataPersistor->get('belvg_factory') ?: [],
                ['store' => $this->getStoreId()]);

            // Clear persisted data
            $this->dataPersistor->clear('belvg_factory');
        }
        $this->proceedCountryIdValue();
        return $this->objectData;
    }

    public function proceedCountryIdValue()
    {
        if (!($this->objectData['materials'] ?? false)) {
            return;
        }
        foreach ($this->objectData['materials'] as &$material) {
            if (!($material['delivery_rules'] ?? false)) {
                continue;
            }
            foreach ($material['delivery_rules'] as &$delivery_rule) {
                if ((isset($delivery_rule['category_id']) && $delivery_rule['category_id'] !== null) && $delivery_rule['category_id'] === 0) {
                    $delivery_rule['category_id'] = '0';
                }
            }
        }
    }

    /**
     * @param array $meta
     * @param array $objectData
     * @param string $field
     * @return void
     */
    private function addUseDefaultCheckbox(array &$meta, array $objectData, string $field): void
    {
        $storeField = $field . '_store';
        $storeValue = $objectData[$storeField] ?? null;
        $useDefault = isset($objectData['use_default'][$field])
            ? (bool) $objectData['use_default'][$field]
            : is_null($storeValue);
        $meta['general']['children'][$field]['arguments']['data']['config'] = [
            'disabled' => $useDefault,
            'service' => [
                'template' => 'ui/form/element/helper/service',
            ]
        ];
    }
}
