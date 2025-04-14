<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Link as LinkResource;

class Link extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(LinkResource::class);
    }

    public function toArray(array $keys = [])
    {
        $data = parent::toArray();

        $data['_type'] = 'link';
        $data['_subtype'] = 'block';
        $data['ref'] = !empty($data['ref_id'])
            ? sprintf('uid:block:%d', $data['ref_id'])
            : null;

        return $data;
    }
}
