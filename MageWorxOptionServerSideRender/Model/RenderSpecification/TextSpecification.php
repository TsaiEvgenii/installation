<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification;

use BelVG\MageWorxOptionServerSideRender\Api\SpecificationInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

class TextSpecification implements SpecificationInterface
{
    public function isSpecifiedBy(ProductCustomOptionInterface $option): bool
    {
        return $option->getType() === 'field';
    }
}
