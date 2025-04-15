<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\Import\SqlProcessor;


use BelVG\MageWorxGroupProductCsv\Model\Import\SqlProcessor\AbstractProcessor;

class MadeInDenmarkOptionPriceProcessor extends AbstractProcessor
{
    const TABLE = 'mageworx_optiontemplates_group_option_type_made_in_denmark_price';
    const TYPE = 'made_in_denmark_option_type';
    const SCHEMA = '(option_type_id,store_id,price,price_type)';

    protected function generateValues($row): string
    {
        return \sprintf('(%s,%d,%.4f,%s)',$row['id'], $row['store_id'],$row['price'],$this->resourceConnection->quote($row['price_type']));
    }
}