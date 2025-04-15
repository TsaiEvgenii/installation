<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\Log;

use BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item as ItemResource;
use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    const EVENT_TYPE_FAILED_SAVING = 'failed_saving';

    const QUEUE_CONSUME = 'consume';
    const QUEUE_PUBLISH = 'publish';

    protected function _construct()
    {
        $this->_init(ItemResource::class);
    }

    public function getEventData()
    {
        $eventData = $this->getData('event_data');
        return is_array($eventData) ? $eventData : [];
    }

    public function setEventData(array $eventData)
    {
        return $this->setData('event_data', $eventData);
    }
}
