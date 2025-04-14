<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block\Parameter;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter\Option
    as OptionResource;

class Option extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(OptionResource::class);
    }

    public function toArray(array $keys = [])
    {
        $data = parent::toArray($keys);
        $data['id'] = $this->getData('option_type_id');
        return $data;
    }
}
