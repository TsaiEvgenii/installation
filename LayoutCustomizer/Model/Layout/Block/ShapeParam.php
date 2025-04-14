<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\ShapeParam
    as ShapeParamResource;

class ShapeParam extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(ShapeParamResource::class);
    }
}
