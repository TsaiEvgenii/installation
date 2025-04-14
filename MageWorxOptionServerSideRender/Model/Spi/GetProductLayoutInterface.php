<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use Magento\Catalog\Model\Product;

interface GetProductLayoutInterface
{
    /**
     * @param Product $product
     * @return LayoutInterface
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function get(Product $product) : LayoutInterface;
}
