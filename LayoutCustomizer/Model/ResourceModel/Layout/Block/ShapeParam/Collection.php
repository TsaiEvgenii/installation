<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\ShapeParam;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\ShapeParam;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\ShapeParam
    as ShapeParamResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ShapeParam::class, ShapeParamResource::class);
    }

    public function addBlockFilter($blockId)
    {
        return $this->addFieldToFilter('block_id', $blockId);
    }
}
