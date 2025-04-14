<?php
namespace BelVG\LayoutCustomizer\Model\DataProvider\Layout;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;

class MaterialDataProvider extends DataProvider
{
    protected \Magento\Framework\App\ResourceConnection $resources;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [])
    {
        $storeId = $request->getParam('store', 0);
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store->getCode());
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        $this->request = $request;
        $this->resources = $context->getResources();

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

    public function addField()
    {

    }

    protected function filterCollection(SearchResultInterface $searchResult)
    {
        if ($searchResult instanceof \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection) {
            $layoutmaterialId = $this->request->getParam('layoutmaterial_id');
            if ($layoutmaterialId) {
                $searchResult
                    ->getSelect()
                    ->join(
                        ['material_props' => $this->resources->getTableName('belvg_layoutmaterial_material_props')],
                        'material_props.layoutmaterialprop_id = main_table.layoutmaterialprop_id',
                        []
                    )
                    ->where('layoutmaterial_id = ?', $layoutmaterialId);
            }
        }
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        //filter collection in case of insertListing xml
        $this->filterCollection($searchResult); //override reason

        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }
}
