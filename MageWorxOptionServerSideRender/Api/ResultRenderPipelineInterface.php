<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api;

use Magento\Catalog\Model\Product\Option;

interface ResultRenderPipelineInterface
{
    /**
     * @param string $result
     * @param Option $option
     * @return string
     */
    public function process(string $result, $option) :string;
}
