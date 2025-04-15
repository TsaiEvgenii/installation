<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

namespace BelVG\MageWorxOptionMadeInDenmark\Model\ResourceModel\MadeInDenmarkOptionTypePrice;

use BelVG\MageWorxOptionMadeInDenmark\Model\MadeInDenmarkOptionTypePrice as Model;
use BelVG\MageWorxOptionMadeInDenmark\Model\ResourceModel\MadeInDenmarkOptionTypePrice as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mageworx_optiontemplates_group_option_type_made_in_denmark_price_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
