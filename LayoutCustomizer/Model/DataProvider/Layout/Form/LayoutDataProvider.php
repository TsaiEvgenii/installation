<?php
namespace BelVG\LayoutCustomizer\Model\DataProvider\Layout\Form;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\CollectionFactory;

class LayoutDataProvider extends \BelVG\LayoutCustomizer\Model\Layout\DataProvider
{
    protected $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        PoolInterface $pool,
        array $meta = [],
        array $data = [])
    {
        $this->pool = $pool;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $dataPersistor,
            $storeManager,
            $meta,
            $data);
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    public function getData()
    {
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }
}
