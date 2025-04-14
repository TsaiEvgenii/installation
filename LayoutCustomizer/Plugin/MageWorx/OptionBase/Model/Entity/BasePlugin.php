<?php


namespace BelVG\LayoutCustomizer\Plugin\MageWorx\OptionBase\Model\Entity;


class BasePlugin
{
    public $layoutCustomizerHelper;

    public function __construct(
        \BelVG\LayoutCustomizer\Helper\Data $layoutCustomizerHelper
    ) {
        $this->layoutCustomizerHelper = $layoutCustomizerHelper;
    }

    public function afterGetOptionsAsArray($subject, $result, $object)
    {
        if (count($result)) {
            $options = $object->getOptions();

            foreach ($options as $option) {
                foreach ($result as &$result_option) {
                    //if ($option->getTitle() === $result_option['title']) {
                    if ($option->getOptionId() === $result_option['id']) {
                        if (isset($result_option['values'])) {
                            foreach ($option->getValues() as $value) {
                                foreach ($result_option['values'] as &$_value) {
                                    if ($value->getOptionTypeId() == $_value['option_type_id']) {
                                        //$_value['mageworx_optiontemplates_group_option_type_id'] = $value->getData('mageworx_optiontemplates_group_option_type_id');
                                        $_value['mageworx_optiontemplates_group_option_type_id'] = $this->layoutCustomizerHelper->getMageWorxOptionTypeIdByOptionTypeId($_value['group_option_value_id']);
                                    }
                                }
                                unset($_value);
                            }
                            unset($value);
                        }
                    }
                }
                unset($result_option);
            }
            unset($option);
        }

        return $result;
    }

}
