<?php
namespace BelVG\Factory\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class Factory extends DataProvider
{
    protected $storeManager;
    protected $pool;

    public function __construct(
        StoreManagerInterface $storeManager,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        PoolInterface $pool,
        array $meta = [],
        array $data = [])
    {
        $this->initStoreManager($storeManager, $request);
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data);
        $this->pool = $pool;
    }

    protected function initStoreManager(
        StoreManagerInterface $storeManager,
        RequestInterface $request)
    {
        $storeId = $request->getParam('store', 0);
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store);
        $this->storeManager = $storeManager;
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
        $data = parent::getData();
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $data;
    }

    protected function prepareUpdateUrl()
    {
        parent::prepareUpdateUrl();
        $storeId = $this->storeManager->getStore()->getId();
        if ($storeId != 0) {
            $this->data['config']['update_url'] .= sprintf('store/%d/', $storeId);
        }
    }
}
