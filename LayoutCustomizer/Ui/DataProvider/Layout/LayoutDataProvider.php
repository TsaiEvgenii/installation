<?php
namespace BelVG\LayoutCustomizer\Ui\DataProvider\Layout;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class LayoutDataProvider //  extends \Magento\Ui\DataProvider\AbstractDataProvider
    extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
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
        $storeId = $request->getParam('store', 0);
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store->getCode());
        $this->storeManager = $storeManager;
        $this->pool = $pool;

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

    /**
     * @param \Magento\Framework\Data\Collection $searchResult
     */
    protected function filterCollection(\Magento\Framework\Data\Collection $searchResult)
    {
        $familyId = $this->request->getParam('family_id');
        if ($familyId !== NULL) {
            $searchResult
                ->getSelect()
                ->where('family_id = ?', $familyId);
        }
    }

    protected function searchResultToOutput(\Magento\Framework\Api\Search\SearchResultInterface $searchResult)
    {
        //filter collection in case of insertListing xml
        $this->filterCollection($searchResult); //override reason

        return parent::searchResultToOutput($searchResult);
    }
}
