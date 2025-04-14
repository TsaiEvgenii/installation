<?php
namespace BelVG\LayoutCustomizer\Model\Layout\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
    const ACTIVE   = 1;
    const INACTIVE = 0;

    public static function getOptionArray()
    {
        return [self::ACTIVE => __('Active'), self::INACTIVE => __('Inactive')];
    }

    public function getAllOptions()
    {
        $options = self::getOptionArray();
        return array_map(
            function($value, $label) {
                return ['value' => $value, 'label' => $label];
            },
            array_keys($options),
            array_values($options));
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
