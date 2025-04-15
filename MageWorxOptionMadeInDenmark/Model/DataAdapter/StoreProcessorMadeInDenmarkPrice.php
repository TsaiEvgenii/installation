<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\DataAdapter;


use BelVG\MageWorxGroupProductCsv\Model\DataAdapter\StoreProcessorPrice;

class StoreProcessorMadeInDenmarkPrice extends StoreProcessorPrice
{
    const FIELDS = ['made_in_denmark_price'];
}