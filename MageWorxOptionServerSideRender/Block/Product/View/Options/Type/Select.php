<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select as SelectParent;

class Select extends SelectParent
{
    public function toHtml()
    {
        /**
         * @var \BelVG\MageWorxOptionServerSideRender\Block\Product\View\Options\Label\RenderSelectList $block
         */
        $block = $this->getChildBlock('option_render_list');
        $render = $block->getRender($this->getOption(), $this->getProduct(), $this->getData());
        return $render->toHtml();
    }
}
