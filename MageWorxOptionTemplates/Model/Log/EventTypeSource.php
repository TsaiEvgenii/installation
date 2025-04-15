<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\Log;

use Magento\Framework\Data\OptionSourceInterface;

class EventTypeSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Failed saving'), 'value' => Item::EVENT_TYPE_FAILED_SAVING]
        ];
    }
}
