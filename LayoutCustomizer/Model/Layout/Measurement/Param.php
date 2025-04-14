<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Measurement;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\Param
    as ParamResource;

class Param extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(ParamResource::class);
    }
}
