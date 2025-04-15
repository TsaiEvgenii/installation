<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\DataAdapter;


use BelVG\MageWorxGroupProductCsv\Model\DataAdapter\AssignedRowRetrieveDataPrice;

class AssignedRowRetrieveDataMadeInDenmarkPrice extends AssignedRowRetrieveDataPrice
{
    const FIELDS_NAME = ['made_in_denmark_price'];
    const MULTI_PRICE_FIELD = 'made_in_denmark_multi_price';

}