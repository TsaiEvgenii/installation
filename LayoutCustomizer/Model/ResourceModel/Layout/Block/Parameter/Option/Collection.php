<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter\Option;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\Parameter\Option;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter\Option
    as OptionResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Option::class, OptionResource::class);
    }

    public function addParameterFilter($parameterId)
    {
        return $this->addFieldToFilter('parameter_id', $parameterId);
    }
}
