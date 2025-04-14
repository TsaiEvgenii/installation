<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api;

interface AdditionalRenderActionInterface
{
    /**
     * @param RenderBlockInterface $block
     */
    public function process(RenderBlockInterface $block) :void;
}
