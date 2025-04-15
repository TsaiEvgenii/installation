<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item;

use BelVG\MageWorxOptionTemplates\Model\Log\Item;
use BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item as ItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Item::class, ItemResource::class);
        $this->_idFieldName = $this->getResource()->getIdFieldName();
    }
}
