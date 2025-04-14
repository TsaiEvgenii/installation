<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2020
 */
declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model\Config;

class PriceFieldsConfig
{
    public function getFields() :iterable
    {
        return [
            'horizontal_frame',
            'vertical_frame',
            'base_price',
            'sqm_price',
            'sqm_price_step2',
            'total_price'
        ];
    }
}
