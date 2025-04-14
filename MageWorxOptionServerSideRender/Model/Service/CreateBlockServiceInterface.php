<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

interface CreateBlockServiceInterface
{
    /**
     * @param ProductCustomOptionInterface $option
     * @return RenderBlockInterface
     */
    public function createBlock(ProductCustomOptionInterface $option) : RenderBlockInterface;
}
