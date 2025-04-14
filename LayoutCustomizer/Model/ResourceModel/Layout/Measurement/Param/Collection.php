<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\Param;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Measurement\Param;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\Param
    as ParamResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Param::class, ParamResource::class);
    }

    public function addMeasurementFilter($measurementId)
    {
        return $this->addFieldToFilter('measurement_id', $measurementId);
    }
}
