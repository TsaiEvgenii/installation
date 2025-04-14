<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction\Param;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\Restriction\Param;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction\Param
    as ParamResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Param::class, ParamResource::class);
    }

    public function addRestrictionFilter($restrictionId)
    {
        return $this->addFieldToFilter('restriction_id', $restrictionId);
    }
}
