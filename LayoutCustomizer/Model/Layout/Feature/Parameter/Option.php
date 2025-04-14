<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Feature\Parameter;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Feature\Parameter\Option
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
