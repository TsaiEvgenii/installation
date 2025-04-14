<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Helper\UI;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Framework\App\Helper\AbstractHelper;

class QtyVisibilityHelper extends AbstractHelper
{
    /**
     * @param AbstractItem $item
     * @return bool
     */
    public function isAllowed(AbstractItem $item) :bool {
        return true;
    }
}
