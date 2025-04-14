<?php
namespace BelVG\LayoutCustomizer\Ui\DataProvider\Layout\Form\Modifier;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use BelVG\LayoutCustomizer\Helper\Layout\StoreData as StoreDataHelper;

class ModelData extends AbstractModifier
{
    protected $storeManager;
    protected $dataPersistor;
    protected $storeDataHelper;

    // NOTE: model data is cached to be used in
    // both self::modifyData() and self::modifyMeta()
    protected $modelData;

    public function __construct(
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        StoreDataHelper $storeDataHelper)
    {
        parent::__construct($coreRegistry);
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
        $this->storeDataHelper = $storeDataHelper;
    }

    public function modifyData(array $data)
    {
        // TODO: replace ID key with 'layout'
        $model = $this->getModel();
        $data[$model->getId() ?: 0] = $this->getModelData();
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $model = $this->getModel();
        if ($storeId != 0) {
            $modelData = $this->getModelData();
            $storeData = $model->getId()
                ? $this->storeDataHelper->load($model->getId(), $storeId, false)
                : [];
            foreach ($this->storeDataHelper->getFields() as $field) {
                $storeValue = isset($storeData[$field]) ? $storeData[$field] : null;
                $useDefault = isset($modelData['use_default'][$field])
                    ? (bool) $modelData['use_default'][$field]
                    : is_null($storeValue);
                $meta['general']['children'][$field]['arguments']['data']['config'] = [
                    'disabled' => $useDefault,
                    'service' => [
                        'template' => 'ui/form/element/helper/service',
                    ]];
            }
        }
        return $meta;
    }

    protected function getModelData()
    {
        if (is_null($this->modelData)) {
            $model = $this->getModel();
            $this->modelData = array_merge(
                $model->getId() ? $model->getData() : [],
                (array) $this->dataPersistor->get('belvg_layoutcustomizer_layout') ?: [],
                ['store' => $model->getStoreId()]);
            $this->dataPersistor->clear('belvg_layoutcustomizer_layout');
        }
        return $this->modelData;
    }
}
