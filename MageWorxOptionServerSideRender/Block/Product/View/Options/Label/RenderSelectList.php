<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Product\View\Options\Label;

use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedOptionProcessor;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;

class RenderSelectList extends AbstractBlock
{
    use SelectedOptionProcessor;
    const TYPE_SELECT = 'select.default';
    const TYPE_SPECIAL_COLOR = 'special_color_select';
    /**
     * @var GetSelectedOptions
     */
    private GetSelectedOptions $selectedOptions;

    /**
     * RenderSelectList constructor.
     * @param Context $context
     * @param GetSelectedOptions $selectedOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        GetSelectedOptions $selectedOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->selectedOptions = $selectedOptions;
    }

    /**
     * @param  $parentBlock
     * @return bool|AbstractBlock
     */
    public function getRender($option, $product, array $data = [])
    {
        $renderName = 'option.select.default';
        $value = $this->getSelectedValue($option, $this->selectedOptions);
        if ($value->getData('is_special_color') !== null) {
            $renderName = 'option.select.special_color';
        }
        $block = $this->getChildBlock($renderName);
        $block->setData(\array_merge($data, $block->getData()));
        $block->setOption($option);
        $block->setProduct($product);
        $block->setSkipJsReloadPrice(1);
        return $block;
    }
}
