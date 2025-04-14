<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

interface RenderSpecificationInterface
{
    /**
     * @param ProductCustomOptionInterface $option
     * @return bool
     */
    public function isSpecifiedBy(ProductCustomOptionInterface $option) :bool;

    /**
     * Return class of block,
     * must implement \BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface
     * @return string
     */
    public function getBlock() :string;

    /**
     * Return path to template
     * @return string
     */
    public function getTemplate() :string;

    /**
     * @param RenderBlockInterface $block
     * @return void
     */
    public function setAdditionalActions(RenderBlockInterface $block) :void;
}
