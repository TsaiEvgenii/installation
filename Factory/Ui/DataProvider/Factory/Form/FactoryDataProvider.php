<?php
namespace BelVG\Factory\Ui\DataProvider\Factory\Form;

use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class FactoryDataProvider extends AbstractDataProvider
{
    protected $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        array $meta = [],
        array $data = [])
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data);
        $this->pool = $pool;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        // ignore
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
