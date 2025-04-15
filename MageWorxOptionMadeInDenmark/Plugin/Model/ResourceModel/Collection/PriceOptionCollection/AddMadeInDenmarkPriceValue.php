<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Plugin\Model\ResourceModel\Collection\PriceOptionCollection;


use BelVG\MageWorxGroupProductCsv\Model\ResourceModel\Collection\PriceOptionCollection;
use Magento\Framework\DB\Select;

class AddMadeInDenmarkPriceValue
{
    public function afterInitMainSelect(
        PriceOptionCollection $source,
        $result,
        $groupIds
    ): void {
        $tableName = $source->getConnection()
            ->getTableName('mageworx_optiontemplates_group_option_type_made_in_denmark_price');
        $source->getSelect()->joinLeft(['made_in_denmark' => $tableName],
            'made_in_denmark.option_type_id=main_table.option_type_id AND made_in_denmark.store_id=main_table.store_id',
            ['made_in_denmark.price as made_in_denmark_price']);
        $source->getSelect()->columns([
            new \Zend_Db_Expr('IFNULL(CONCAT("[",'
                . 'GROUP_CONCAT(CONCAT("{",'
                . '"\"store_id\":", made_in_denmark.store_id, ",\"made_in_denmark_price\":", made_in_denmark.price, ",\"price_type\":\"", made_in_denmark.price_type,'
                . ' "\"}")),'
                . '"]"), "{}") as made_in_denmark_multi_price')
        ]);

    }
    public function afterAddPriceOption(
        PriceOptionCollection $source,
        Select $result,
        $groupIds
    ): Select {
        $tableName = $result->getConnection()
            ->getTableName('mageworx_optiontemplates_group_option_type_made_in_denmark_price');
        $result->joinLeft(['made_in_denmark' => $tableName],
            'made_in_denmark.option_type_id=main_table.option_id',
            ['made_in_denmark.price as made_in_denmark_price']);
        $result->columns([new \Zend_Db_Expr('IFNULL(CONCAT("[",'
            . 'GROUP_CONCAT(CONCAT("{",'
            . '"\"store_id\":", made_in_denmark.store_id, ",\"made_in_denmark_price\":", made_in_denmark.price, ",\"price_type\":\"", made_in_denmark.price_type,'
            . ' "\"}")),'
            . '"]"), "{}") as made_in_denmark_multi_price')]);

        return $result;
    }


}