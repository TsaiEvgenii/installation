<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification\AdditionalRenderAction;

use BelVG\MageWorxOptionServerSideRender\Api\AdditionalRenderActionInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;

class DummyRenderAction implements AdditionalRenderActionInterface
{
    public function process(RenderBlockInterface $block): void
    {
    }
}
