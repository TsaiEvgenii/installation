<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block\Restriction;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Restriction\Param
    as ParamResource;

class Param extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(ParamResource::class);
    }
}
