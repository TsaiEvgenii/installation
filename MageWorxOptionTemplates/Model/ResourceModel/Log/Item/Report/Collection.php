<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item\Report;

use BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item as LogItemResource;
use BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item\Collection as LogItemCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider;

class Collection extends LogItemCollection implements SearchResultInterface
{
    protected $aggregations;

    protected function _construct()
    {
        $this->_init(DataProvider\Document::class, LogItemResource::class);
    }

    public function getAggregations()
    {
        return $this->aggregation;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setItems(array $items = null)
    {
        return $this;
    }
}
