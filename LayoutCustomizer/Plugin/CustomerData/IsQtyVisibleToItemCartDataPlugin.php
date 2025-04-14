<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\CustomerData;

class IsQtyVisibleToItemCartDataPlugin
{
    /**
     * @param $subject
     * @param $result
     * @param $item
     * @return mixed
     */
    public function afterGetItemData($subject, $result, $item)
    {
        $result['is_qty_visible'] = true;

        return $result;
    }
}
