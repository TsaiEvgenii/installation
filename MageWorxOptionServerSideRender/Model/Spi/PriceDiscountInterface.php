<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

interface PriceDiscountInterface
{
    /**
     * @param float $price
     * @return float
     */
    public function modifier(float $price, $product) :float;
}
