<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Param;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Feature\Param;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Param
    as ParamResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Param::class, ParamResource::class);
    }

    public function addFeatureFilter($featureId)
    {
        return $this->addFieldToFilter('feature_id', $featureId);
    }
}
