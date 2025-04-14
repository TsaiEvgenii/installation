<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

interface SpecificationInterface
{
    /**
     * @param ProductCustomOptionInterface $option
     * @return bool
     */
    public function isSpecifiedBy(ProductCustomOptionInterface $option) :bool;
}
