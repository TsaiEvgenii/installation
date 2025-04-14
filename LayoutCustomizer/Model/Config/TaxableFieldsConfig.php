<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */
declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model\Config;

class TaxableFieldsConfig
{
    public function getFields() :iterable
    {
        return [
            'horizontal_frame',
            'vertical_frame',
            'base_price',
            'sqm_price',
            'sqm_price_step2'
        ];
    }
}
