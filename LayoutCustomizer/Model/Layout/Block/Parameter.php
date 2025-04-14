<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Block;

use BelVG\LayoutCustomizer\Model\Layout\Block\Parameter\Option;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\Parameter as ParameterResource;

class Parameter extends \Magento\Framework\Model\AbstractModel
{
    protected $options = [];

    protected function _construct()
    {
        $this->_init(ParameterResource::class);
    }

    public function addOption(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }

    public function toArray(array $keys = [])
    {
        $data = parent::toArray();

        $data['_type'] = 'parameter';
        $data['_subtype'] = 'block';

        if (empty($keys) || in_array('options', $keys)) {
            $data['options'] = array_map(function($option) {
                return $option->toArray();
            }, $this->options);
        }

        return $data;
    }
}
