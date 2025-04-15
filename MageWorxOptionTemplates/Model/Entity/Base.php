<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
namespace BelVG\MageWorxOptionTemplates\Model\Entity;

use \Magento\Catalog\Model\Product\Option;
use \Magento\Catalog\Model\Product\Option\Value;
use MageWorx\OptionBase\Model\Entity\Base as MageworxBase;

class Base extends MageworxBase
{

    public function getOptionsForGroup($object, $currentOptionsIds = null)
    {
        $options = $object->getOptions();

        if ($options == null) {
            $options = [];
        }

        $showPrice = true;
        $results = [];

        foreach ($options as $option) {
            /* @var $option Option */
            if (array_search($option->getMageworxGroupOptionId(), $currentOptionsIds) !== false){
                $result = [];
                $result['id'] = $option->getOptionId();
                $result['item_count'] = $object->getItemCount();
                $result['option_id'] = $option->getOptionId();
                $result['mageworx_option_id'] = $this->getMageworxOptionId($option);
//$result['mageworx_group_option_id'] = $option->getMageworxOptionId(); //$this->getGroupMageworxOptionId($option);
                $result['title'] = $option->getTitle();
                $result['type'] = $option->getType();
                $result['is_require'] = $option->getIsRequire();
                $result['sort_order'] = $option->getSortOrder();
                $result['can_edit_price'] = $object->getCanEditPrice();
                $result['group_option_id'] = $option->getGroupOptionId();
                if (!empty($object->getGroupId())) {
                    $result['group_id'] = $object->getGroupId();
                }

                if ($option->getGroupByType() == Option::OPTION_GROUP_SELECT &&
                    $option->getValues()
                ) {
                    $itemCount = 0;
                    foreach ($option->getValues() as $value) {
                        $i = $value->getOptionTypeId();
                        /* @var $value Value */
                        $result['values'][$i] = [
                            'item_count' => max($itemCount, $value->getOptionTypeId()),
                            'option_id' => $value->getOptionId(),
                            'option_type_id' => $value->getOptionTypeId(),
                            'mageworx_option_type_id' => $this->getMageworxOptionTypeId($value),
                            'mageworx_group_option_type_id' => $this->getGroupMageworxOptionTypeId($value),
                            'title' => $value->getTitle(),
                            'price' => $showPrice ?
                                $this->getPriceValue($value->getPrice(), $value->getPriceType()) :
                                0,
                            'price_type' => $showPrice && $value->getPriceType() ?
                                $value->getPriceType() :
                                'fixed',
                            'sku' => $value->getSku(),
                            'sort_order' => $value->getSortOrder(),
                            'group_option_value_id' => $value->getGroupOptionValueId(),
                        ];
                        if (!empty($object->getGroupId())) {
                            $result['values'][$i]['group_id'] = $object->getGroupId();
                        }
                        // Add option value attributes specified in the third-party modules to the option values
                        $result['values'][$i] = $this->addSpecificOptionValueAttributes($result['values'][$i], $value);
                    }
                } else {
                    $result['price'] = $showPrice ? $this->getPriceValue(
                        $option->getPrice(),
                        $option->getPriceType()
                    ) : 0;
                    $result['price_type'] = $option->getPriceType() ? $option->getPriceType() : 'fixed';
                    $result['sku'] = $option->getSku();
                    $result['max_characters'] = $option->getMaxCharacters();
                    $result['file_extension'] = $option->getFileExtension();
                    $result['image_size_x'] = $option->getImageSizeX();
                    $result['image_size_y'] = $option->getImageSizeY();
                    $result['inside_outside_color'] = $option->getInsideOutsideColor();
                    $result['values'] = null;
                }

                // Add option attributes specified in the third-party modules to the option
                $result = $this->addSpecificOptionAttributes($result, $option);
                $results[$option->getOptionId()] = $result;
            }

        }

        return $results;
    }

    public function getOptionsEmptyArray($object)
    {
        $options = $object->getOptions();

        if ($options == null) {
            $options = [];
        }

        $showPrice = true;
        $results = [];

        foreach ($options as $option) {
            /* @var $option Option */
            $result = [];
            $result['id'] = $option->getOptionId();
            $result['item_count'] = $object->getItemCount();
            $result['option_id'] = $option->getOptionId();
            $result['mageworx_option_id'] = $this->getMageworxOptionId($option);
//$result['mageworx_group_option_id'] = $option->getMageworxOptionId(); //$this->getGroupMageworxOptionId($option);
            $result['group_option_id'] = $option->getGroupOptionId();
            if (!empty($object->getGroupId())) {
                $result['group_id'] = $object->getGroupId();
            }

            if ($option->getGroupByType() == Option::OPTION_GROUP_SELECT &&
                $option->getValues()
            ) {
                $itemCount = 0;
                foreach ($option->getValues() as $value) {
                    $i = $value->getOptionTypeId();
                    /* @var $value Value */
                    $result['values'][$i] = [
                        'item_count' => max($itemCount, $value->getOptionTypeId()),
                        'option_id' => $value->getOptionId(),
                        'option_type_id' => $value->getOptionTypeId(),
                        'mageworx_option_type_id' => $this->getMageworxOptionTypeId($value),
                        'mageworx_group_option_type_id' => $this->getGroupMageworxOptionTypeId($value),
                        'group_option_value_id' => $value->getGroupOptionValueId(),
                    ];
                    if (!empty($object->getGroupId())) {
                        $result['values'][$i]['group_id'] = $object->getGroupId();
                    }
                    // Add option value attributes specified in the third-party modules to the option values
//                    $result['values'][$i] = $this->addSpecificOptionValueAttributes($result['values'][$i], $value);
                }
            } else {

                $result['values'] = null;
            }

            // Add option attributes specified in the third-party modules to the option
//            $result = $this->addSpecificOptionAttributes($result, $option);
            $results[$option->getOptionId()] = $result;
        }

        return $results;
    }

}
