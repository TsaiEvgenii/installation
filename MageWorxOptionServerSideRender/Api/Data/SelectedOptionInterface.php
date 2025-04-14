<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api\Data;

use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;

interface SelectedOptionInterface
{
    const OPTION_ID='option_id';
    const OPTION_KEY='option_key';
    const VALUE = 'value';

    public function getOptionId() :int;

    public function getOptionKey() :string;

    public function getValue() :string;

    public function getObjectValue() :?SelectedOptionValue;
}
