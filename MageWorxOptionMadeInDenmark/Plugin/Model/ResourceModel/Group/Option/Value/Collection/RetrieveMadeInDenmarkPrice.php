<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Plugin\Model\ResourceModel\Group\Option\Value\Collection;


use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\Value\Collection;

class RetrieveMadeInDenmarkPrice
{
    public function __construct(
        protected Http $request,
        protected State $appState,
    ){

    }
    public function afterAddPriceToResult(
        Collection $subject,
        Collection $result,
        $storeId
    ) {
        $optionTypeTable = $result->getTable('mageworx_optiontemplates_group_option_type_made_in_denmark_price');
        $priceExpr = $result->getConnection()->getCheckSql(
            'made_in_denmark_store_value_price.price IS NULL',
            'made_in_denmark_default_value_price.price',
            'made_in_denmark_store_value_price.price'
        );
        $priceTypeExpr = $result->getConnection()->getCheckSql(
            'made_in_denmark_store_value_price.price_type IS NULL',
            'made_in_denmark_default_value_price.price_type',
            'made_in_denmark_store_value_price.price_type'
        );

        $joinExprDefault = 'made_in_denmark_default_value_price.option_type_id = main_table.option_type_id AND ' .
            $result->getConnection()->quoteInto(
                'made_in_denmark_default_value_price.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        $joinExprStore = 'made_in_denmark_store_value_price.option_type_id = main_table.option_type_id AND ' .
            $result->getConnection()->quoteInto('made_in_denmark_store_value_price.store_id = ?', $storeId);
        $result->getSelect()->joinLeft(
            ['made_in_denmark_default_value_price' => $optionTypeTable],
            $joinExprDefault,
            ['made_in_denmark_default_price'      => 'price',
             'made_in_denmark_default_price_type' => 'price_type'
            ]
        )->joinLeft(
            ['made_in_denmark_store_value_price' => $optionTypeTable],
            $joinExprStore,
            [
                'made_in_denmark_store_price'      => 'price',
                'made_in_denmark_store_price_type' => 'price_type',
                'made_in_denmark_price'            => $priceExpr,
                'made_in_denmark_price_type'       => $priceTypeExpr
            ]
        );

        return $result;
    }
}