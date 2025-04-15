<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */

namespace BelVG\MageWorxOptionMadeInDenmark\Model;

use BelVG\MageWorxOptionMadeInDenmark\Model\ResourceModel\MadeInDenmarkOptionTypePrice as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class MadeInDenmarkOptionTypePrice extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mageworx_optiontemplates_group_option_type_made_in_denmark_price_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
