<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

namespace BelVG\MageWorxOptionMadeInDenmark\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MadeInDenmarkOptionTypePrice extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mageworx_optiontemplates_group_option_type_made_in_denmark_price_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('mageworx_optiontemplates_group_option_type_made_in_denmark_price', 'option_type_price_id');
        $this->_useIsObjectNew = true;
    }
}
