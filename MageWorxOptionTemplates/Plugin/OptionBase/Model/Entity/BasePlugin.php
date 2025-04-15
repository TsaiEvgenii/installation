<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Plugin\OptionBase\Model\Entity;


class BasePlugin
{

    public function afterGetOptionsAsArray($subject, $result, $object)
    {
        if (count($result)) {
            $options = $object->getOptions();

            foreach ($options as $option) {
                foreach ($result as &$result_option) {
                    if ($option->getOptionId() === $result_option['id']) {
                        if (!isset($result_option['inside_outside_color']) && empty($result_option['inside_outside_color'])) {
                            $result_option['inside_outside_color'] = '';
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
