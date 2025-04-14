<?php
namespace BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Link;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use BelVG\LayoutCustomizer\Model\Layout\Block\Link;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Link as LinkResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Link::class, LinkResource::class);
    }

    public function addBlockFilter($blockId)
    {
        return $this->addFieldToFilter('block_id', $blockId);
    }
}
