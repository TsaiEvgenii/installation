<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction\Param;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\MeasurementRestriction\Param;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\MeasurementRestriction\Param
    as ParamResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Param::class, ParamResource::class);
    }

    public function addMeasurementRestrictionFilter($measurementRestrictionId)
    {
        return $this->addFieldToFilter('measurement_restriction_id', $measurementRestrictionId);
    }
}
