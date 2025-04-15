<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb
{
    protected $_serializableFields = ['event_data' => [[], []]];

    protected function _construct()
    {
        $this->_init('belvg_option_templates_log', 'item_id');
    }
}
